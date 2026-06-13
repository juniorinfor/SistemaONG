<?php

namespace App\Console\Commands;

use App\Models\Edital;
use App\Models\Institution;
use App\Services\ClaudeExtractionService;
use Illuminate\Console\Command;

class ExtractEditais extends Command
{
    protected $signature   = 'editais:extract {--limit=10 : Máximo de editais a processar por vez}';
    protected $description = 'Processa editais sem resumo usando Claude para extração de dados';

    public function handle(ClaudeExtractionService $claude): int
    {
        $institution = Institution::where('slug', 'promessa')->firstOrFail();
        $limit       = (int) $this->option('limit');

        $pendentes = Edital::where('institution_id', $institution->id)
            ->whereNull('resumo')
            ->whereNotNull('raw_text')
            ->limit($limit)
            ->get();

        if ($pendentes->isEmpty()) {
            $this->info('Nenhum edital pendente de extração.');
            return Command::SUCCESS;
        }

        $this->info("Processando {$pendentes->count()} edital(is) via Claude...");

        foreach ($pendentes as $edital) {
            $this->line("  → {$edital->titulo}");

            $extracted = $claude->extrairEdital($edital->raw_text);

            if (isset($extracted['error'])) {
                $this->warn("    Erro: {$extracted['error']}");
                continue;
            }

            $edital->update([
                'titulo'          => $extracted['titulo']          ?? $edital->titulo,
                'area'            => $extracted['area']            ?? $edital->area,
                'resumo'          => $extracted['resumo']          ?? null,
                'criterios'       => $extracted['criterios']       ?? null,
                'valor_min'       => $extracted['valor_min']       ?? $edital->valor_min,
                'valor_max'       => $extracted['valor_max']       ?? $edital->valor_max,
                'prazo_inscricao' => $extracted['prazo_inscricao'] ?? $edital->prazo_inscricao,
                'prazo_execucao'  => $extracted['prazo_execucao']  ?? $edital->prazo_execucao,
            ]);

            $this->line("    ✔ Extraído (área: {$edital->fresh()->area})");
        }

        $this->info('Extração concluída.');
        return Command::SUCCESS;
    }
}
