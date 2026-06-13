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

    private function call(string $prompt): array
    {
        if (empty($this->apiKey)) {
            return ['error' => 'ANTHROPIC_API_KEY não configurada'];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model'      => $this->model,
                'max_tokens' => 800,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
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
