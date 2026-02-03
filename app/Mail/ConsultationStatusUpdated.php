<?php

namespace App\Mail;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsultationStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Consultation $consultation;
    public ?string $note;

    public function __construct(Consultation $consultation, ?string $note = null)
    {
        $this->consultation = $consultation->loadMissing(['user', 'category', 'handler']);
        $this->note = $note;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name');

        if (!$fromAddress) {
            throw new \RuntimeException('MAIL_FROM_ADDRESS belum diset di env/config.');
        }

        return $this
            ->from($fromAddress, $fromName)
            ->subject('Update Status Konsultasi #' . ($this->consultation->ticket_id ?? 'N/A'))
            ->view('emails.consultation_status_updated')
            ->with([
                'consultation' => $this->consultation,
                'user'         => $this->consultation->user,
                'category'     => $this->consultation->category,
                'handler'      => $this->consultation->handler,
                'note'         => $this->note,
            ]);
    }
}