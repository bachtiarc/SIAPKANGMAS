<?php

namespace App\Mail;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsultationStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $consultation;
    public $note;

    public function __construct(Consultation $consultation, $note)
    {
        $this->consultation = $consultation;
        $this->note = $note;
    }

    public function build()
    {
        return $this->subject('Update Status Konsultasi #' . $this->consultation->ticket_id)
                    ->view('emails.consultation_status_updated');
    }
}