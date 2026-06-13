<?php
namespace App\Mail;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentExpirationAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Document $document,
        public int $daysLeft,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match(true) {
            $this->daysLeft === 0 => "[URGENTE] Documento vence HOJE — {$this->document->documentType->name}",
            $this->daysLeft === 1 => "[URGENTE] Documento vence amanhã — {$this->document->documentType->name}",
            default               => "[Alerta] Documento vence em {$this->daysLeft} dias — {$this->document->documentType->name}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.document-expiration');
    }
}
