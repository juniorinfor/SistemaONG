<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeExtractionService
{
    private string $apiKey;
    private string $model = 'claude-haiku-4-5-20251001'; // mais barato
    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
    }

    /**
     * Extrai campos estruturados de um texto bruto de edital.
     * Envia no máximo ~3000 chars para minimizar tokens.
     */
    public function extrairEdital(string $texto, string $idioma = 'pt'): array
    {
        $trecho = mb_substr($texto, 0, 3000);

        $prompt = <<<PROMPT
Analise o texto abaixo de um edital/chamada pública para ONGs e extraia as informações em JSON.
Se o texto estiver em outro idioma, traduza os campos "resumo" e "criterios" para português brasileiro.
Responda APENAS com JSON válido, sem markdown, sem explicações.

Formato esperado:
{
  "titulo": "string — título completo do edital",
  "area": "string — área temática (assistência social, educação, saúde, cultura, meio ambiente, criança e adolescente, mulher, habitação, esporte, outro)",
  "valor_min": number ou null,
  "valor_max": number ou null,
  "prazo_inscricao": "YYYY-MM-DD" ou null,
  "prazo_execucao": "YYYY-MM-DD" ou null,
  "resumo": "string — resumo em português de até 300 caracteres",
  "criterios": "string — lista dos requisitos/documentos exigidos para habilitação, em português, separados por \\n"
}

Texto do edital:
{$trecho}
PROMPT;

        return $this->call($prompt);
    }

    /**
     * Extrai campos de um EDITAL EM ARQUIVO (PDF ou imagem) usando a visão nativa do Claude.
     * Não exige biblioteca de parser no servidor — envia o arquivo como bloco document/image.
     */
    public function extrairEditalDeArquivo(string $absolutePath, string $mimeType): array
    {
        if (!is_file($absolutePath)) {
            return ['error' => 'Arquivo não encontrado para análise'];
        }

        $data = base64_encode(file_get_contents($absolutePath));

        $prompt = <<<PROMPT
O documento anexado é um edital/chamada pública para ONGs. Extraia as informações em JSON.
Se estiver em outro idioma, traduza "resumo" e "criterios" para português brasileiro.
Responda APENAS com JSON válido, sem markdown, sem explicações.

Formato esperado:
{
  "titulo": "string — título completo do edital",
  "area": "string — área temática (assistência social, educação, saúde, cultura, meio ambiente, criança e adolescente, mulher, habitação, esporte, outro)",
  "valor_min": number ou null,
  "valor_max": number ou null,
  "prazo_inscricao": "YYYY-MM-DD" ou null,
  "prazo_execucao": "YYYY-MM-DD" ou null,
  "resumo": "string — resumo em português de até 400 caracteres",
  "criterios": "string — lista dos requisitos/documentos exigidos para habilitação, um por linha separados por \\n"
}
PROMPT;

        if (str_contains($mimeType, 'pdf')) {
            $bloco = [
                'type'   => 'document',
                'source' => ['type' => 'base64', 'media_type' => 'application/pdf', 'data' => $data],
            ];
        } else {
            $bloco = [
                'type'   => 'image',
                'source' => ['type' => 'base64', 'media_type' => $mimeType, 'data' => $data],
            ];
        }

        return $this->callWithContent([$bloco, ['type' => 'text', 'text' => $prompt]], 1500, 90);
    }

    /**
     * Verifica compatibilidade entre os critérios do edital e os documentos da instituição.
     * Usa apenas o campo "criterios" (já armazenado) + lista de tipos de documentos.
     * Estimativa: ~600 tokens por chamada.
     */
    public function verificarCompatibilidade(string $criterios, array $documentosDisponiveis): array
    {
        $listaDocs = implode("\n", $documentosDisponiveis);

        $prompt = <<<PROMPT
Você é um assistente para ONGs brasileiras.

REQUISITOS DO EDITAL (critérios de habilitação):
{$criterios}

DOCUMENTOS QUE A ONG POSSUI ATUALMENTE:
{$listaDocs}

Compare os requisitos com os documentos disponíveis e responda APENAS com JSON válido:
{
  "score": número de 0 a 100 (percentual de compatibilidade),
  "matched": ["documento que atende requisito X", ...],
  "missing": ["requisito Y não atendido", ...],
  "observacao": "string curta em português com avaliação geral"
}
PROMPT;

        return $this->call($prompt);
    }

    /**
     * Sugere 3 projetos do portfólio da ONG ranqueados pela aderência ao edital.
     * Não inventa do zero — usa os projetos já cadastrados.
     */
    public function sugerirProjetos(array $edital, array $projetos): array
    {
        $editalTxt = "TÍTULO: " . ($edital['titulo'] ?? '—') . "\n"
            . "ÁREA: " . ($edital['area'] ?? '—') . "\n"
            . "VALOR: " . ($edital['valor'] ?? '—') . "\n"
            . "RESUMO: " . ($edital['resumo'] ?? '—') . "\n"
            . "CRITÉRIOS: " . mb_substr($edital['criterios'] ?? '—', 0, 1200);

        $portfolio = '';
        foreach ($projetos as $p) {
            $portfolio .= "ID {$p['id']} | {$p['titulo']} | área: {$p['area']} | faixa: {$p['valor']}\n"
                . "  resumo: " . mb_substr($p['descricao'] ?? '', 0, 220) . "\n";
        }

        $prompt = <<<PROMPT
Você é um consultor de captação de recursos para ONGs brasileiras.

EDITAL EM ANÁLISE:
{$editalTxt}

PORTFÓLIO DE PROJETOS DA ONG (já cadastrados):
{$portfolio}

Selecione os 3 projetos do portfólio MAIS ADERENTES a este edital e ranqueie do mais ao menos aderente.
Para cada um, avalie a aderência (0-100), explique por que combina e o que ajustar para encaixar no edital.
Responda APENAS com JSON válido, sem markdown:
{
  "sugestoes": [
    {
      "project_id": número (ID do projeto do portfólio),
      "titulo": "string — título do projeto",
      "aderencia": número de 0 a 100,
      "justificativa": "string curta — por que esse projeto combina com o edital",
      "ajustes": "string curta — o que adaptar para encaixar nas exigências do edital"
    }
  ]
}
PROMPT;

        return $this->call($prompt);
    }

    private function call(string $prompt): array
    {
        return $this->callWithContent($prompt, 1200, 45);
    }

    /**
     * Chamada genérica à API. $content pode ser string (texto simples)
     * ou array de blocos (texto + document/image).
     */
    private function callWithContent(string|array $content, int $maxTokens = 800, int $timeout = 30): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'ANTHROPIC_API_KEY não configurada'];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout($timeout)->post($this->baseUrl, [
                'model'      => $this->model,
                'max_tokens' => $maxTokens,
                'messages'   => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Claude API error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['error' => 'Erro na API: ' . $response->status()];
            }

            $text = $response->json('content.0.text', '');
            // Remove possível markdown fence
            $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
            $text = preg_replace('/```\s*$/m', '', $text);

            return json_decode(trim($text), true) ?? ['error' => 'JSON inválido na resposta'];

        } catch (\Throwable $e) {
            Log::error('Claude API exception', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
