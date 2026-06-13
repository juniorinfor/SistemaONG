<?php
/**
 * Script de sincronização de editais — roda no GitHub Actions.
 * Fontes: Transferegov (gov federal BR) + D-Portal/IATI (internacional)
 */

$ingestUrl    = getenv('INGEST_URL');
$ingestToken  = getenv('INGEST_TOKEN');
$dadosGovKey  = getenv('DADOS_GOV_KEY');

if (!$ingestUrl || !$ingestToken) {
    echo "ERRO: variáveis INGEST_URL e INGEST_TOKEN são obrigatórias.\n";
    exit(1);
}

$editais = [];
$log     = [];

// ---------------------------------------------------------------
// FONTE 1: Transferegov — tenta múltiplos endpoints conhecidos
// ---------------------------------------------------------------
echo "→ Buscando Transferegov...\n";

$tgovEndpoints = [
    'https://api.plataformamaisbrasil.gov.br/convenios/v1/chamadas-publicas?situacao=ABERTA&tamanhoPagina=50',
    'https://transfere.gov.br/api/convenios/chamadas?situacao=ABERTA&size=50',
    'https://www.gov.br/transferencias-voluntarias/pt-br/acesso-a-informacao/chamadas-publicas/chamadas-abertas.json',
];

$tgovFound = false;
foreach ($tgovEndpoints as $url) {
    echo "  Tentando: {$url}\n";
    [$status, $body] = fetchRaw($url);
    echo "  Status: {$status}\n";

    if ($status === 200 && $body) {
        $data  = json_decode($body, true);
        $items = $data['data'] ?? $data['content'] ?? $data['result'] ?? $data ?? [];
        if (!empty($items) && is_array($items)) {
            echo "  ✔ Transferegov: " . count($items) . " item(s)\n";
            foreach ($items as $item) {
                $fonteId = 'tgov_' . ($item['id'] ?? md5(json_encode($item)));
                $titulo  = $item['titulo'] ?? $item['nome'] ?? $item['objeto'] ?? '';
                if (empty($titulo)) continue;
                $editais[] = [
                    'fonte'           => 'transferegov',
                    'fonte_id'        => $fonteId,
                    'titulo'          => $titulo,
                    'link_oficial'    => $item['linkEdital'] ?? $item['urlEdital'] ?? null,
                    'valor_min'       => $item['valorMinimo'] ?? null,
                    'valor_max'       => $item['valorMaximo'] ?? $item['valorTotal'] ?? null,
                    'prazo_inscricao' => formatDate($item['dataEncerramentoInscricao'] ?? $item['dataFim'] ?? null),
                    'raw_text'        => implode("\n", array_filter([
                        $titulo,
                        $item['objeto'] ?? $item['descricao'] ?? '',
                        $item['requisitos'] ?? '',
                    ])),
                ];
            }
            $tgovFound = true;
            break;
        }
    }
    // Loga resposta para debug
    $log[] = "Transferegov [{$url}] → HTTP {$status}: " . substr($body ?? '', 0, 200);
}

if (!$tgovFound) {
    echo "  ⚠ Transferegov: nenhum endpoint respondeu com dados\n";
}

// ---------------------------------------------------------------
// FONTE 2: Dados.gov.br (CKAN) — datasets de chamadas públicas
// ---------------------------------------------------------------
echo "→ Buscando Dados.gov.br...\n";

$dadosGovUrls = [
    'https://dados.gov.br/api/3/action/package_search?q=chamada+publica+ONG&rows=50&sort=metadata_modified+desc',
    'https://dados.gov.br/api/3/action/package_search?q=edital+organizacao+sociedade+civil&rows=50',
];

$dadosGovFound = false;
foreach ($dadosGovUrls as $url) {
    echo "  Tentando: {$url}\n";

    // Tenta Bearer JWT primeiro, depois chave CKAN, depois sem auth
    $attempts = [
        ['Authorization: Bearer ' . $dadosGovKey, 'X-CKAN-API-Key: ' . $dadosGovKey],
        ['Authorization: Bearer ' . $dadosGovKey],
        ['X-CKAN-API-Key: ' . $dadosGovKey],
        [], // sem auth
    ];

    foreach ($attempts as $headers) {
        [$status, $body] = fetchRaw($url, $headers);
        if ($status === 200 && $body) {
            $data    = json_decode($body, true);
            $success = $data['success'] ?? false;
            $results = $data['result']['results'] ?? [];

            if ($success && !empty($results)) {
                echo "  ✔ Dados.gov.br: " . count($results) . " dataset(s)\n";
                foreach ($results as $ds) {
                    $titulo = $ds['title'] ?? $ds['name'] ?? '';
                    if (empty($titulo)) continue;

                    // Pega URL do recurso principal (PDF ou página)
                    $link = null;
                    foreach ($ds['resources'] ?? [] as $res) {
                        if (in_array(strtolower($res['format'] ?? ''), ['pdf', 'html', 'htm', 'url'])) {
                            $link = $res['url'] ?? null;
                            break;
                        }
                    }

                    $editais[] = [
                        'fonte'        => 'dados_gov',
                        'fonte_id'     => 'dgov_' . ($ds['id'] ?? md5($titulo)),
                        'titulo'       => $titulo,
                        'link_oficial' => $link ?? ('https://dados.gov.br/dados/conjuntos-dados/' . ($ds['name'] ?? '')),
                        'raw_text'     => implode("\n", array_filter([
                            $titulo,
                            $ds['notes'] ?? $ds['description'] ?? '',
                        ])),
                    ];
                }
                $dadosGovFound = true;
                break 2;
            }
        }
        $log[] = "Dados.gov.br [{$url}] → HTTP {$status}";
    }
}

if (!$dadosGovFound) {
    echo "  ⚠ Dados.gov.br: sem resultados\n";
}

// ---------------------------------------------------------------
// FONTE 3: D-Portal (espelho oficial do IATI — mais estável)
// Internacional com Brasil como beneficiário
// ---------------------------------------------------------------
echo "→ Buscando D-Portal (IATI internacional)...\n";

$dportalEndpoints = [
    'https://d-portal.org/q.json?form=act&recipient_country_code=BR&status=2&limit=50&fields=aid,titles,activity_dates,budgets,descriptions',
    'https://d-portal.org/q.json?form=act&recipient_country_code=BR&limit=50',
];

$dportalFound = false;
foreach ($dportalEndpoints as $url) {
    echo "  Tentando: {$url}\n";
    [$status, $body] = fetchRaw($url);
    echo "  Status: {$status}\n";

    if ($status === 200 && $body) {
        $data  = json_decode($body, true);
        // D-Portal retorna {"rows":[...]} ou {"total":N,"results":[...]}
        $items = $data['rows'] ?? $data['result'] ?? $data['activities'] ?? $data['data'] ?? [];

        if (isset($data['total']) && isset($data['results'])) {
            $items = $data['results'];
        }

        if (!empty($items) && is_array($items)) {
            echo "  ✔ D-Portal: " . count($items) . " item(s)\n";
            foreach ($items as $item) {
                $aid     = $item['aid'] ?? $item['iati_identifier'] ?? md5(json_encode($item));
                $fonteId = 'iati_' . $aid;
                $titulo  = $item['title'] ?? $item['titles'][0]['title'] ?? '';
                if (is_array($titulo)) $titulo = $titulo['narrative'] ?? reset($titulo) ?? '';
                if (empty($titulo)) continue;

                $descricao = $item['description'] ?? $item['descriptions'][0]['description'] ?? '';
                if (is_array($descricao)) $descricao = $descricao['narrative'] ?? reset($descricao) ?? '';

                $editais[] = [
                    'fonte'           => 'iati',
                    'fonte_id'        => $fonteId,
                    'titulo'          => $titulo,
                    'link_oficial'    => "https://d-portal.org/ctrack.html#view=act&aid={$aid}",
                    'prazo_inscricao' => formatDate($item['date_end_planned'] ?? $item['activity_date_end'] ?? null),
                    'raw_text'        => trim($titulo . "\n" . $descricao),
                ];
            }
            $dportalFound = true;
            break;
        }
    }
    $log[] = "D-Portal [{$url}] → HTTP {$status}: " . substr($body ?? '', 0, 200);
}

if (!$dportalFound) {
    echo "  ⚠ D-Portal: nenhum endpoint respondeu com dados\n";
}

// ---------------------------------------------------------------
// Debug: imprime log de endpoints que falharam
// ---------------------------------------------------------------
if (!empty($log)) {
    echo "\n--- DEBUG ---\n";
    foreach ($log as $l) echo $l . "\n";
    echo "-------------\n";
}

// ---------------------------------------------------------------
// ENVIA para o Laravel (mesmo se 0 editais — não falha)
// ---------------------------------------------------------------
echo "\n→ Coletados: " . count($editais) . " edital(is)\n";

if (empty($editais)) {
    echo "Nenhum edital novo para enviar. Encerrando sem erro.\n";
    exit(0); // não falha — apenas não havia dados disponíveis
}

echo "→ Enviando para ingest...\n";
$payload = json_encode(['editais' => $editais]);
$ch = curl_init($ingestUrl);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'X-Ingest-Token: ' . $ingestToken,
    ],
]);
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Resposta HTTP: {$status}\n";
echo $response . "\n";

if ($status >= 400) {
    echo "ERRO no ingest (HTTP {$status}).\n";
    exit(1);
}

echo "\nSincronização concluída.\n";

// ---------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------
function fetchRaw(string $url, array $extraHeaders = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 PromessaDocs-Sync/1.0',
        CURLOPT_HTTPHEADER     => array_merge(
            ['Accept: application/json, text/json, */*'],
            $extraHeaders
        ),
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, $body ?: null];
}

function formatDate(?string $date): ?string
{
    if (!$date) return null;
    try { return (new DateTime($date))->format('Y-m-d'); }
    catch (Exception) { return null; }
}
