<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Edital;
use App\Models\Institution;
use App\Services\ClaudeExtractionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EditalExtractController extends Controller
{
    public function handle(Request $request, ClaudeExtractionService $claude): JsonResponse
    {
        $token = $request->header('X-Ingest-Token');
        if ($token !== config('services.ingest.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $institution = Institution::where('slug', 'promessa')->firstOrFail();

        $pendentes = Edital::where('institution_id', $institution->id)
            ->whereNull('resumo')
            ->whereNotNull('raw_text')
            ->limit(10) // processa 10 por vez para não dar timeout
            ->get();

        $processados = 0;
        foreach ($pendentes as $edital) {
            $extracted = $claude->extrairEdital($edital->raw_text);
            if (isset($extracted['error'])) continue;

            $edital->update([
                'titulo'          => $extracted['titulo']          ?? $edital->titulo,
                'area'            => $extracted['area']            ?? $edital->area,
                'resumo'          => $extracted['resumo']          ?? null,
                'criterios'       => $extracted['criterios']       ?? null,
                'valor_min'       => $extracted['valor_min']       ?? $edital->valor_min,
                'valor_max'       => $extracted['valor_max']       ?? $edital->valor_max,
                'prazo_inscricao' => $extracted['prazo_inscricao'] ?? $edital->prazo_inscricao?->toDateString(),
                'prazo_execucao'  => $extracted['prazo_execucao']  ?? $edital->prazo_execucao?->toDateString(),
            ]);

            $processados++;
        }

        return response()->json([
            'processados' => $processados,
            'pendentes'   => max(0, $pendentes->count() - $processados),
            'message'     => "Extração concluída: {$processados} edital(is) processado(s).",
        ]);
    }
}
