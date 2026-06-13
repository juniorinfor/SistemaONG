<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Edital;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class EditalIngestController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // Valida token secreto
        $token = $request->header('X-Ingest-Token');
        if ($token !== config('services.ingest.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $editais = $request->input('editais', []);
        if (empty($editais)) {
            return response()->json(['error' => 'Nenhum edital recebido'], 422);
        }

        $institution = Institution::where('slug', 'promessa')->firstOrFail();
        $inserted = 0;
        $skipped  = 0;

        foreach ($editais as $item) {
            $fonteId = $item['fonte_id'] ?? null;
            $fonte   = $item['fonte']    ?? 'github';

            // Evita duplicatas
            if ($fonteId && Edital::where('fonte', $fonte)->where('fonte_id', $fonteId)->exists()) {
                $skipped++;
                continue;
            }

            Edital::create([
                'institution_id'  => $institution->id,
                'titulo'          => $item['titulo']       ?? 'Sem título',
                'area'            => $item['area']         ?? null,
                'fonte'           => $fonte,
                'fonte_id'        => $fonteId,
                'link_oficial'    => $item['link_oficial'] ?? null,
                'valor_min'       => $item['valor_min']    ?? null,
                'valor_max'       => $item['valor_max']    ?? null,
                'prazo_inscricao' => $this->parseDate($item['prazo_inscricao'] ?? null),
                'prazo_execucao'  => $this->parseDate($item['prazo_execucao']  ?? null),
                'resumo'          => $item['resumo']       ?? null,
                'criterios'       => $item['criterios']    ?? null,
                'raw_text'        => $item['raw_text']     ?? null,
                'status'          => 'aberto',
                'synced_at'       => now(),
            ]);

            $inserted++;
        }

        // Remove editais com prazo vencido há mais de 7 dias
        Edital::where('institution_id', $institution->id)->expirados()->delete();

        return response()->json([
            'inserted' => $inserted,
            'skipped'  => $skipped,
            'message'  => "Ingest concluído: {$inserted} novo(s), {$skipped} ignorado(s).",
        ]);
    }

    private function parseDate(?string $date): ?string
    {
        if (!$date) return null;
        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
