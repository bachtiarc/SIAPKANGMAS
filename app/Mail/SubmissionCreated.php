<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Submission $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission->loadMissing(['user', 'category']);
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
            ->subject('Permohonan Informasi Berhasil - ' . ($this->submission->ticket_id ?? 'N/A'))
            ->view('emails.submission-created')
            ->with([
                'submission' => $this->submission,
                'user'       => $this->submission->user,
                'category'   => $this->submission->category, 
            ]);
    }
}