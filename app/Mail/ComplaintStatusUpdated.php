<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Complaint $complaint,
        public ?string $notes = null
    ) {}

    public function build()
    {
        return $this->subject('Update Status Pengaduan #' . ($this->complaint->ticket_number ?? $this->complaint->id))
            ->view('emails.complaint_status_updated');
    }
}