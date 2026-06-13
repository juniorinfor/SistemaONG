<?php
namespace App\Console\Commands;

use App\Mail\DocumentExpirationAlert;
use App\Models\Document;
use App\Models\Institution;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckDocumentExpirations extends Command
{
    protected $signature   = 'docs:check-expirations {--dry-run : Lista alertas sem enviar e-mails}';
    protected $description = 'Verifica documentos próximos do vencimento e envia alertas por e-mail';

    public function handle(): int
    {
        $thresholds  = config('documents.alert_thresholds', [30, 10, 1]);
        $isDryRun    = $this->option('dry-run');
        $institution = Institution::where('slug', 'promessa')->first();

        if (! $institution) {
            $this->error('Instituição não encontrada.');
            return self::FAILURE;
        }

        $recipients = User::all()->map->email->filter()->values();

        if ($recipients->isEmpty()) {
            $this->warn('Nenhum usuário cadastrado para receber alertas.');
            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($thresholds as $days) {
            $targetDate = now()->addDays($days)->toDateString();

            $documents = Document::where('institution_id', $institution->id)
                ->where('is_current', true)
                ->whereDate('expires_at', $targetDate)
                ->with(['documentType', 'person'])
                ->get();

            foreach ($documents as $doc) {
                $alreadySent = NotificationLog::where('document_id', $doc->id)
                    ->where('days_before_expiry', $days)
                    ->where('status', 'sent')
                    ->whereDate('sent_at', today())
                    ->exists();

                if ($alreadySent) {
                    $skipped++;
                    continue;
                }

                if ($isDryRun) {
                    $this->line("[dry-run] {$doc->documentType->name} vence em {$days}d ({$doc->expires_at->format('d/m/Y')})");
                    $sent++;
                    continue;
                }

                try {
                    foreach ($recipients as $email) {
                        Mail::to($email)->send(new DocumentExpirationAlert($doc, $days));
                    }

                    NotificationLog::create([
                        'institution_id'    => $institution->id,
                        'document_id'       => $doc->id,
                        'channel'           => 'email',
                        'notifiable_type'   => Institution::class,
                        'notifiable_id'     => $institution->id,
                        'days_before_expiry'=> $days,
                        'status'            => 'sent',
                        'sent_at'           => now(),
                    ]);

                    $sent++;
                    $this->info("Alerta enviado: {$doc->documentType->name} (vence em {$days}d)");

                } catch (\Throwable $e) {
                    NotificationLog::create([
                        'institution_id'    => $institution->id,
                        'document_id'       => $doc->id,
                        'channel'           => 'email',
                        'notifiable_type'   => Institution::class,
                        'notifiable_id'     => $institution->id,
                        'days_before_expiry'=> $days,
                        'status'            => 'failed',
                        'error_message'     => $e->getMessage(),
                        'sent_at'           => now(),
                    ]);

                    $this->error("Falha ao enviar para {$doc->documentType->name}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Concluído: {$sent} alerta(s) enviado(s), {$skipped} ignorado(s) (já enviados hoje).");
        return self::SUCCESS;
    }
}
