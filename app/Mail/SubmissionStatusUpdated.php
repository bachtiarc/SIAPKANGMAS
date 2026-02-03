<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Submission $submission;
    public ?string $note;

    public function __construct(Submission $submission, ?string $note = null)
    {
        $this->submission = $submission->loadMissing(['user', 'category', 'handler']);
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
            ->subject('Update Status Tiket #' . ($this->submission->ticket_id ?? 'N/A'))
            ->view('emails.submission_status_updated')
            ->with([
                'submission' => $this->submission,
                'user'       => $this->submission->user,
                'category'   => $this->submission->category,
                'handler'    => $this->submission->handler,
                'note'       => $this->note,
            ]);
    }
}