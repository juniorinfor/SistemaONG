<?php
/**
 * Script de sincronização de editais — roda no GitHub Actions.
 * Faz chamadas às APIs externas (Transferegov, IATI) e envia os resultados
 * para o endpoint de ingest do Laravel via HTTP.
 */

$ingestUrl   = getenv('INGEST_URL');   // https://promessa.ong.br/documentos/public/api/editais/ingest
$ingestToken = getenv('INGEST_TOKEN'); // valor do INGEST_SECRET no .env do servidor

if (!$ingestUrl || !$ingestToken) {
    echo "ERRO: variáveis INGEST_URL e INGEST_TOKEN são obrigatórias.\n";
    exit(1);
}

$editais = [];
$erros   = [];

// ---------------------------------------------------------------
// FONTE 1: Transferegov — Chamadas Públicas Abertas
// ---------------------------------------------------------------
echo "→ Buscando Transferegov...\n";
$tgov = fetchJson('https://api.transferegov.sistema.gov.br/chamadas/v1/chamadas-publicas?situacao=ABERTA&tamanhoPagina=100');

if ($tgov !== null) {
    $items = $tgov['data'] ?? $tgov['content'] ?? $tgov['result'] ?? [];
    echo "  Transferegov: " . count($items) . " item(s) encontrado(s)\n";

    foreach ($items as $item) {
        $fonteId = 'tgov_' . ($item['id'] ?? md5(json_encode($item)));
        $titulo  = $item['titulo'] ?? $item['nome'] ?? '';
        if (empty($titulo)) continue;

        $editais[] = [
            'fonte'           => 'transferegov',
            'fonte_id'        => $fonteId,
            'titulo'          => $titulo,
            'link_oficial'    => $item['linkEdital'] ?? $item['urlEdital'] ?? null,
            'valor_min'       => $item['valorMinimo'] ?? null,
            'valor_max'       => $item['valorMaximo'] ?? $item['valorTotal'] ?? null,
            'prazo_inscricao' => formatDate($item['dataEncerramentoInscricao'] ?? null),
            'raw_text'        => implode("\n", array_filter([
                $titulo,
                $item['objeto']     ?? $item['descricao'] ?? '',
                $item['requisitos'] ?? '',
            ])),
        ];
    }
} else {
    $erros[] = 'Transferegov: falha na requisição';
    echo "  ERRO: não foi possível acessar a API do Transferegov\n";
}

// ---------------------------------------------------------------
// FONTE 2: IATI — Atividades com Brasil como beneficiário
// ---------------------------------------------------------------
echo "→ Buscando IATI...\n";

// Tenta endpoints alternativos do IATI
$iatiEndpoints = [
    'https://iati.cloud/api/activities?recipient_country_code=BR&activity_status_code=2&format=json&limit=50',
    'https://api.iatistandard.org/activities?recipient-country=BR&activity-status=2&format=json&limit=50',
];

$iatiItems = [];
foreach ($iatiEndpoints as $endpoint) {
    $iati = fetchJson($endpoint);
    if ($iati !== null) {
        $iatiItems = $iati['results'] ?? $iati['data'] ?? $iati['iati-activities'] ?? [];
        if (!empty($iatiItems)) {
            echo "  IATI: " . count($iatiItems) . " item(s) via {$endpoint}\n";
            break;
        }
    }
}

if (empty($iatiItems)) {
    $erros[] = 'IATI: nenhum resultado encontrado';
    echo "  IATI: sem resultados nos endpoints testados\n";
}

foreach ($iatiItems as $item) {
    $iatiId  = $item['iati_identifier'] ?? $item['iati-identifier'] ?? md5(json_encode($item));
    $fonteId = 'iati_' . $iatiId;

    $titulo = iatiText($item['title'] ?? []);
    if (empty($titulo)) continue;

    $descricao = iatiText($item['description'] ?? []);
    $endDate   = null;
    foreach ($item['activity_date'] ?? $item['activity-date'] ?? [] as $d) {
        if (($d['type'] ?? '') === '3') { $endDate = $d['iso_date'] ?? $d['iso-date'] ?? null; break; }
    }

    $editais[] = [
        'fonte'           => 'iati',
        'fonte_id'        => $fonteId,
        'titulo'          => $titulo,
        'link_oficial'    => "https://d-portal.org/ctrack.html#view=act&aid={$iatiId}",
        'prazo_inscricao' => formatDate($endDate),
        'raw_text'        => $titulo . "\n" . $descricao,
    ];
}

// ---------------------------------------------------------------
// ENVIA para o Laravel
// ---------------------------------------------------------------
echo "\n→ Enviando " . count($editais) . " edital(is) para ingest...\n";

if (empty($editais)) {
    echo "Nenhum edital para enviar.\n";
    if (!empty($erros)) {
        echo "Erros: " . implode(', ', $erros) . "\n";
        exit(1);
    }
    exit(0);
}

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
    echo "ERRO no ingest.\n";
    exit(1);
}

echo "\nSincronização concluída com sucesso.\n";

// ---------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------
function fetchJson(string $url): ?array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_USERAGENT      => 'PromessaDocs-Sync/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200 || !$body) return null;
    return json_decode($body, true);
}

function iatiText(array $data): string
{
    if (empty($data)) return '';
    foreach ($data as $item) {
        if (($item['lang'] ?? '') === 'pt') return $item['narrative'] ?? '';
    }
    foreach ($data as $item) {
        if (($item['lang'] ?? '') === 'en') return $item['narrative'] ?? '';
    }
    return $data[0]['narrative'] ?? '';
}

function formatDate(?string $date): ?string
{
    if (!$date) return null;
    try {
        return (new DateTime($date))->format('Y-m-d');
    } catch (Exception) {
        return null;
    }
}
