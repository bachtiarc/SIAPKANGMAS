<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Complaint $complaint;
    public ?string $notes;

    public function __construct(Complaint $complaint, ?string $notes = null)
    {
        $this->complaint = $complaint->loadMissing(['user', 'category', 'handler']);
        $this->notes = $notes;
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
            ->subject('Update Status Pengaduan #' . ($this->complaint->ticket_number ?? $this->complaint->id))
            ->view('emails.complaint_status_updated')
            ->with([
                'complaint' => $this->complaint,
                'user'      => $this->complaint->user,
                'category'  => $this->complaint->category,
                'handler'   => $this->complaint->handler,
                'notes'     => $this->notes,
            ]);
    }
}