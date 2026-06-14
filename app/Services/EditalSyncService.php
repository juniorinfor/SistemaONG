<?php

namespace App\Services;

use App\Models\Edital;
use App\Models\Institution;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EditalSyncService
{
    public function __construct(private ClaudeExtractionService $claude) {}

    /**
     * Executa sincronização de todas as fontes.
     * Retorna contagem de novos editais adicionados.
     */
    public function syncAll(Institution $institution, ?int $limit = null): array
    {
        $results = [];
        $results['transferegov']   = $this->syncTransferegov($institution, $limit);
        $results['iati']           = $this->syncIati($institution, $limit);
        $results['dportal']        = $this->syncDPortal($institution, $limit);
        $results['dados_gov']      = $this->syncDadosGov($institution, $limit);
        $results['querido_diario'] = $this->syncQueridoDiario($institution, $limit);
        $results['undp']           = $this->syncUndp($institution, $limit);
        $results['eu_grants']      = $this->syncEuGrants($institution, $limit);
        return $results;
    }

    // ---------------------------------------------------------------
    // FONTE 1: Transferegov (governo federal brasileiro)
    // ---------------------------------------------------------------
    public function syncTransferegov(Institution $institution, ?int $limit = null): int
    {
        try {
            $response = Http::timeout(20)->get(
                'https://api.transferegov.sistema.gov.br/chamadas/v1/chamadas-publicas',
                ['situacao' => 'ABERTA', 'tamanhoPagina' => 50]
            );

            if ($response->failed()) {
                Log::warning('Transferegov API failed', ['status' => $response->status()]);
                return 0;
            }

            $items = $response->json('data', $response->json('content', []));
            if ($limit) $items = array_slice($items, 0, $limit);
            $count = 0;

            foreach ($items as $item) {
                $fonteId = 'tgov_' . ($item['id'] ?? md5(json_encode($item)));

                if (Edital::where('fonte', 'transferegov')->where('fonte_id', $fonteId)->exists()) {
                    continue;
                }

                // No modo sample, salva os dados brutos sem chamar a IA
                $extracted = [];
                if (!$limit) {
                    $texto = implode("\n", array_filter([
                        $item['titulo'] ?? $item['nome'] ?? '',
                        $item['objeto'] ?? $item['descricao'] ?? '',
                        $item['requisitos'] ?? '',
                    ]));
                    $extracted = $this->claude->extrairEdital($texto, 'pt');
                    if (isset($extracted['error'])) $extracted = [];
                }

                Edital::create([
                    'institution_id'  => $institution->id,
                    'titulo'          => $extracted['titulo'] ?? ($item['titulo'] ?? $item['nome'] ?? 'Sem título'),
                    'area'            => $extracted['area'] ?? null,
                    'fonte'           => 'transferegov',
                    'fonte_id'        => $fonteId,
                    'link_oficial'    => $item['linkEdital'] ?? $item['urlEdital'] ?? null,
                    'valor_min'       => $extracted['valor_min'] ?? ($item['valorMinimo'] ?? null),
                    'valor_max'       => $extracted['valor_max'] ?? ($item['valorMaximo'] ?? $item['valorTotal'] ?? null),
                    'prazo_inscricao' => $extracted['prazo_inscricao'] ?? $this->parseDate($item['dataEncerramentoInscricao'] ?? null),
                    'prazo_execucao'  => $extracted['prazo_execucao'] ?? null,
                    'resumo'          => $extracted['resumo'] ?? ($limit ? '[amostra — sem extração IA]' : null),
                    'criterios'       => $extracted['criterios'] ?? null,
                    'status'          => 'aberto',
                    'synced_at'       => now(),
                ]);

                $count++;
            }

            return $count;

        } catch (\Throwable $e) {
            Log::error('Transferegov sync error', ['message' => $e->getMessage()]);
            return 0;
        }
    }

    // ---------------------------------------------------------------
    // FONTE 2: IATI (editais internacionais — Brasil como beneficiário)
    // ---------------------------------------------------------------
    public function syncIati(Institution $institution, ?int $limit = null): int
    {
        try {
            $response = Http::timeout(30)->get('https://iati.cloud/api/activities', [
                'recipient_country_code' => 'BR',
                'activity_status_code'   => '2',
                'format'                 => 'json',
                'limit'                  => $limit ?? 30,
                'fields'                 => 'iati_identifier,title,description,activity_date,value',
            ]);

            if ($response->failed()) {
                Log::warning('IATI API failed', ['status' => $response->status()]);
                return 0;
            }

            $items = $response->json('results', []);
            $count = 0;

            foreach ($items as $item) {
                $fonteId = 'iati_' . ($item['iati_identifier'] ?? md5(json_encode($item)));

                if (Edital::where('fonte', 'iati')->where('fonte_id', $fonteId)->exists()) {
                    continue;
                }

                $titulo    = $this->iatiText($item['title'] ?? []);
                $descricao = $this->iatiText($item['description'] ?? []);

                if (empty($titulo)) continue;

                // No modo sample, não chama a IA
                $extracted = [];
                if (!$limit) {
                    $extracted = $this->claude->extrairEdital($titulo . "\n" . $descricao, 'en');
                    if (isset($extracted['error'])) $extracted = [];
                }

                $endDate = collect($item['activity_date'] ?? [])
                    ->firstWhere('type', '3')['iso_date'] ?? null;
                $valor = collect($item['budget'] ?? $item['transaction'] ?? [])->pluck('value')->max();

                Edital::create([
                    'institution_id'  => $institution->id,
                    'titulo'          => $extracted['titulo'] ?? $titulo,
                    'area'            => $extracted['area'] ?? null,
                    'fonte'           => 'iati',
                    'fonte_id'        => $fonteId,
                    'link_oficial'    => "https://d-portal.org/ctrack.html#view=act&aid={$item['iati_identifier']}",
                    'valor_min'       => null,
                    'valor_max'       => $extracted['valor_max'] ?? $valor,
                    'prazo_inscricao' => $extracted['prazo_inscricao'] ?? $this->parseDate($endDate),
                    'resumo'          => $extracted['resumo'] ?? ($limit ? '[amostra — sem extração IA] ' . mb_substr($descricao, 0, 200) : mb_substr($descricao, 0, 300)),
                    'criterios'       => $extracted['criterios'] ?? null,
                    'status'          => 'aberto',
                    'synced_at'       => now(),
                ]);

                $count++;
            }

            return $count;

        } catch (\Throwable $e) {
            Log::error('IATI sync error', ['message' => $e->getMessage()]);
            return 0;
        }
    }

    // ---------------------------------------------------------------
    // FONTE 3: D-Portal (espelho IATI — público, sem autenticação)
    // ---------------------------------------------------------------
    public function syncDPortal(Institution $institution, ?int $limit = null): int
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://d-portal.org/q.json', [
                    'form'                  => 'act',
                    'recipient_country_code' => 'BR',
                    'status'                => '2',
                    'limit'                 => $limit ?? 50,
                    'fields'                => 'aid,titles,activity_dates,budgets,descriptions',
                ]);

            if ($response->failed()) {
                Log::warning('D-Portal API failed', ['status' => $response->status()]);
                return 0;
            }

            $items = $response->json('rows', []);
            $count = 0;

            foreach ($items as $item) {
                $aid     = $item['aid'] ?? md5(json_encode($item));
                $fonteId = 'iati_' . $aid;

                if (Edital::where('fonte', 'iati')->where('fonte_id', $fonteId)->exists()) {
                    continue;
                }

                $titulo    = $item['title'] ?? '';
                $descricao = $item['description'] ?? '';
                if (empty($titulo)) continue;

                $extracted = [];
                if (!$limit) {
                    $extracted = $this->claude->extrairEdital($titulo . "\n" . $descricao, 'en');
                    if (isset($extracted['error'])) $extracted = [];
                }

                Edital::create([
                    'institution_id'  => $institution->id,
                    'titulo'          => $extracted['titulo'] ?? $titulo,
                    'area'            => $extracted['area'] ?? null,
                    'fonte'           => 'iati',
                    'fonte_id'        => $fonteId,
                    'link_oficial'    => "https://d-portal.org/ctrack.html#view=act&aid={$aid}",
                    'valor_min'       => null,
                    'valor_max'       => $extracted['valor_max'] ?? null,
                    'prazo_inscricao' => $extracted['prazo_inscricao'] ?? null,
                    'resumo'          => $extracted['resumo'] ?? ($limit ? '[amostra]' : mb_substr($descricao, 0, 300)),
                    'criterios'       => $extracted['criterios'] ?? null,
                    'status'          => 'aberto',
                    'synced_at'       => now(),
                ]);

                $count++;
            }

            return $count;

        } catch (\Throwable $e) {
            Log::error('D-Portal sync error', ['message' => $e->getMessage()]);
            return 0;
        }
    }

    // ---------------------------------------------------------------
    // FONTE 4: Querido Diário (Open Knowledge Brasil) — últimos 30 dias
    // ---------------------------------------------------------------
    public function syncQueridoDiario(Institution $institution, ?int $limit = null): int
    {
        $since   = now()->subDays(30)->format('Y-m-d');
        $queries = [
            'chamamento publico organizacao sociedade civil',
            'edital assistencia social ONG',
        ];

        $count = 0;

        foreach ($queries as $q) {
            try {
                $response = Http::timeout(20)->get('https://queridodiario.ok.org.br/api/gazettes', [
                    'querystring' => $q,
                    'since'       => $since,
                    'size'        => $limit ?? 20,
                    'sort_by'     => 'relevance',
                ]);

                if ($response->failed()) {
                    Log::warning('Querido Diário falhou', ['status' => $response->status(), 'q' => $q]);
                    continue;
                }

                $gazettes = $response->json('gazettes', []);

                foreach ($gazettes as $gazette) {
                    $fonteId = 'qd_' . md5($gazette['url'] ?? $gazette['date'] . $gazette['territory_id']);

                    if (Edital::where('fonte', 'querido_diario')->where('fonte_id', $fonteId)->exists()) {
                        continue;
                    }

                    $excerpts = $gazette['excerpts'] ?? [];
                    $rawText  = "[{$gazette['territory_name']} — {$gazette['date']}]\n"
                              . implode("\n\n", $excerpts);

                    $extracted = [];
                    if (!$limit) {
                        $extracted = $this->claude->extrairEdital(mb_substr($rawText, 0, 3000), 'pt');
                        if (isset($extracted['error'])) $extracted = [];
                    }

                    $titulo = $extracted['titulo']
                        ?? "Diário {$gazette['territory_name']} — " . \Carbon\Carbon::parse($gazette['date'])->format('d/m/Y');

                    Edital::create([
                        'institution_id'  => $institution->id,
                        'titulo'          => $titulo,
                        'area'            => $extracted['area'] ?? null,
                        'fonte'           => 'querido_diario',
                        'fonte_id'        => $fonteId,
                        'link_oficial'    => $gazette['url'] ?? null,
                        'resumo'          => $extracted['resumo'] ?? mb_substr(implode(' ', $excerpts), 0, 300),
                        'criterios'       => $extracted['criterios'] ?? null,
                        'prazo_inscricao' => $extracted['prazo_inscricao'] ?? null,
                        'valor_min'       => $extracted['valor_min'] ?? null,
                        'valor_max'       => $extracted['valor_max'] ?? null,
                        'status'          => 'aberto',
                        'synced_at'       => now(),
                    ]);

                    $count++;
                    if ($limit && $count >= $limit) return $count;
                }

            } catch (\Throwable $e) {
                Log::error('Querido Diário sync error', ['message' => $e->getMessage(), 'q' => $q]);
            }
        }

        return $count;
    }

    // ---------------------------------------------------------------
    // FONTE 5: Dados.gov.br (CKAN) — datasets de chamadas públicas
    // ---------------------------------------------------------------
    public function syncDadosGov(Institution $institution, ?int $limit = null): int
    {
        $key = config('services.dados_gov.key');

        $queries = [
            'chamada publica organizacao sociedade civil',
            'edital ONG assistencia social',
        ];

        $headers = array_filter([
            'Accept' => 'application/json',
            'Authorization' => $key ? "Bearer {$key}" : null,
        ]);

        foreach ($queries as $q) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders($headers)
                    ->get('https://dados.gov.br/api/3/action/package_search', [
                        'q'    => $q,
                        'rows' => $limit ?? 50,
                        'sort' => 'metadata_modified desc',
                    ]);

                if ($response->failed()) {
                    Log::warning('Dados.gov.br falhou', ['status' => $response->status(), 'q' => $q]);
                    continue;
                }

                $results = $response->json('result.results', []);
                if (empty($results)) continue;

                $count = 0;
                foreach ($results as $ds) {
                    $titulo = $ds['title'] ?? $ds['name'] ?? '';
                    if (empty($titulo)) continue;

                    $fonteId = 'dgov_' . ($ds['id'] ?? md5($titulo));
                    if (Edital::where('fonte', 'dados_gov')->where('fonte_id', $fonteId)->exists()) {
                        continue;
                    }

                    $link = null;
                    foreach ($ds['resources'] ?? [] as $res) {
                        if (in_array(strtolower($res['format'] ?? ''), ['pdf', 'html', 'htm', 'url'])) {
                            $link = $res['url'] ?? null;
                            break;
                        }
                    }

                    $rawText = implode("\n", array_filter([$titulo, $ds['notes'] ?? '']));
                    $extracted = [];
                    if (!$limit) {
                        $extracted = $this->claude->extrairEdital($rawText, 'pt');
                        if (isset($extracted['error'])) $extracted = [];
                    }

                    Edital::create([
                        'institution_id'  => $institution->id,
                        'titulo'          => $extracted['titulo'] ?? $titulo,
                        'area'            => $extracted['area'] ?? null,
                        'fonte'           => 'dados_gov',
                        'fonte_id'        => $fonteId,
                        'link_oficial'    => $link ?? ('https://dados.gov.br/dados/conjuntos-dados/' . ($ds['name'] ?? '')),
                        'resumo'          => $extracted['resumo'] ?? ($limit ? '[amostra]' : mb_substr($ds['notes'] ?? '', 0, 300)),
                        'criterios'       => $extracted['criterios'] ?? null,
                        'status'          => 'aberto',
                        'synced_at'       => now(),
                    ]);
                    $count++;
                }

                if ($count > 0) return $count;

            } catch (\Throwable $e) {
                Log::error('Dados.gov.br sync error', ['message' => $e->getMessage(), 'q' => $q]);
            }
        }

        return 0;
    }

    // ---------------------------------------------------------------
    // FONTE 6: UNDP Procurement Notices — projetos ONU com Brasil
    // ---------------------------------------------------------------
    public function syncUndp(Institution $institution, ?int $limit = null): int
    {
        // RSS público de notificações de procurement do PNUD
        $urls = [
            'https://procurement-notices.undp.org/rss.cfm?country=BRA',
            'https://procurement-notices.undp.org/rss.cfm',
        ];

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(20)
                    ->withHeaders(['Accept' => 'application/rss+xml, application/xml, text/xml'])
                    ->get($url);

                if ($response->failed() || empty($response->body())) continue;

                $xml = @simplexml_load_string($response->body());
                if (!$xml) continue;

                $items = $xml->channel->item ?? [];
                $count = 0;

                foreach ($items as $item) {
                    $fonteId = 'undp_' . md5((string) ($item->link ?? $item->guid ?? ''));
                    if (Edital::where('fonte', 'undp')->where('fonte_id', $fonteId)->exists()) continue;

                    $titulo    = (string) ($item->title ?? '');
                    $descricao = strip_tags((string) ($item->description ?? ''));
                    if (empty($titulo)) continue;

                    $rawText   = $titulo . "\n" . $descricao;
                    $extracted = [];
                    if (!$limit) {
                        $extracted = $this->claude->extrairEdital(mb_substr($rawText, 0, 3000), 'en');
                        if (isset($extracted['error'])) $extracted = [];
                    }

                    $prazo = null;
                    if (!empty((string) ($item->pubDate ?? ''))) {
                        try { $prazo = \Carbon\Carbon::parse((string) $item->pubDate)->addDays(30)->toDateString(); }
                        catch (\Throwable) {}
                    }

                    Edital::create([
                        'institution_id'  => $institution->id,
                        'titulo'          => $extracted['titulo'] ?? $titulo,
                        'area'            => $extracted['area'] ?? 'cooperação internacional',
                        'fonte'           => 'undp',
                        'fonte_id'        => $fonteId,
                        'link_oficial'    => (string) ($item->link ?? ''),
                        'resumo'          => $extracted['resumo'] ?? mb_substr($descricao, 0, 300),
                        'criterios'       => $extracted['criterios'] ?? null,
                        'prazo_inscricao' => $extracted['prazo_inscricao'] ?? $prazo,
                        'valor_max'       => $extracted['valor_max'] ?? null,
                        'status'          => 'aberto',
                        'synced_at'       => now(),
                    ]);

                    $count++;
                    if ($limit && $count >= $limit) return $count;
                }

                if ($count > 0 || !empty($items)) return $count;

            } catch (\Throwable $e) {
                Log::error('UNDP sync error', ['message' => $e->getMessage(), 'url' => $url]);
            }
        }

        return 0;
    }

    // ---------------------------------------------------------------
    // FONTE 7: EU Grants (Portal Financiamento UE) — elegíveis p/ Brasil
    // ---------------------------------------------------------------
    public function syncEuGrants(Institution $institution, ?int $limit = null): int
    {
        try {
            // API pública do portal EU Funding & Tenders (chave SEDIA é pública)
            $response = Http::timeout(20)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://api.tech.ec.europa.eu/search-api/prod/rest/search', [
                    'apiKey'     => 'SEDIA',
                    'text'       => 'civil society social Brazil',
                    'pageSize'   => $limit ?? 20,
                    'pageNumber' => 1,
                    'scope'      => 'SEDIA',
                ]);

            if ($response->failed()) {
                Log::warning('EU Grants API falhou', ['status' => $response->status()]);
                return 0;
            }

            $results = $response->json('results', $response->json('hits.hits', []));
            $count   = 0;

            foreach ($results as $item) {
                $src     = $item['_source'] ?? $item;
                $id      = $src['identifier'] ?? $src['id'] ?? md5(json_encode($src));
                $fonteId = 'eu_' . $id;

                if (Edital::where('fonte', 'eu_grants')->where('fonte_id', $fonteId)->exists()) continue;

                $titulo = $src['title'] ?? $src['name'] ?? '';
                if (is_array($titulo)) $titulo = $titulo['en'] ?? reset($titulo) ?? '';
                if (empty($titulo)) continue;

                $descricao = $src['description'] ?? $src['summary'] ?? '';
                if (is_array($descricao)) $descricao = $descricao['en'] ?? reset($descricao) ?? '';

                $rawText   = $titulo . "\n" . $descricao;
                $extracted = [];
                if (!$limit) {
                    $extracted = $this->claude->extrairEdital(mb_substr($rawText, 0, 3000), 'en');
                    if (isset($extracted['error'])) $extracted = [];
                }

                $link = $src['callDetailsUrl']
                    ?? $src['url']
                    ?? ('https://ec.europa.eu/info/funding-tenders/opportunities/portal/screen/opportunities/topic-details/' . strtolower($id));

                Edital::create([
                    'institution_id'  => $institution->id,
                    'titulo'          => $extracted['titulo'] ?? $titulo,
                    'area'            => $extracted['area'] ?? 'cooperação europeia',
                    'fonte'           => 'eu_grants',
                    'fonte_id'        => $fonteId,
                    'link_oficial'    => $link,
                    'resumo'          => $extracted['resumo'] ?? mb_substr($descricao, 0, 300),
                    'criterios'       => $extracted['criterios'] ?? null,
                    'prazo_inscricao' => $extracted['prazo_inscricao']
                        ?? $this->parseDate($src['deadlineDate'] ?? $src['endDate'] ?? null),
                    'valor_min'       => $extracted['valor_min'] ?? null,
                    'valor_max'       => $extracted['valor_max']
                        ?? ($src['budgetMax'] ?? $src['budget'] ?? null),
                    'status'          => 'aberto',
                    'synced_at'       => now(),
                ]);

                $count++;
                if ($limit && $count >= $limit) return $count;
            }

            return $count;

        } catch (\Throwable $e) {
            Log::error('EU Grants sync error', ['message' => $e->getMessage()]);
            return 0;
        }
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------
    private function iatiText(array $data): string
    {
        if (empty($data)) return '';
        // Prefere PT, depois EN
        $pt = collect($data)->firstWhere('lang', 'pt');
        if ($pt) return $pt['narrative'] ?? '';
        $en = collect($data)->firstWhere('lang', 'en');
        if ($en) return $en['narrative'] ?? '';
        return collect($data)->first()['narrative'] ?? '';
    }

    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;
        try {
            return \Carbon\Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
