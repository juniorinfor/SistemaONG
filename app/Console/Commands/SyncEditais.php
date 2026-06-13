<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Models\Edital;
use App\Services\EditalSyncService;
use Illuminate\Console\Command;

class SyncEditais extends Command
{
    protected $signature   = 'editais:sync';
    protected $description = 'Sincroniza editais de fontes externas (Transferegov, IATI)';

    public function handle(EditalSyncService $sync): int
    {
        $this->info('Iniciando sincronização de editais...');

        $institution = Institution::where('slug', 'promessa')->firstOrFail();

        $results = $sync->syncAll($institution);

        foreach ($results as $fonte => $count) {
            $this->line("  [{$fonte}] {$count} novo(s) edital(is)");
        }

        // Remove editais vencidos há mais de 7 dias
        $removed = Edital::where('institution_id', $institution->id)
            ->expirados()
            ->count();

        Edital::where('institution_id', $institution->id)
            ->expirados()
            ->delete(); // soft delete

        $total = array_sum($results);
        $this->info("Sincronização concluída: {$total} novo(s) adicionado(s), {$removed} removido(s).");

        return Command::SUCCESS;
    }
}
