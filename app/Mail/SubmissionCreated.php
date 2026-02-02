<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SubmissionCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // pakai FROM dari config/.env biar SMTP ga nolak
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Permohonan Informasi Berhasil - ' . $this->submission->ticket_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-created',
            with: [
                'submission' => $this->submission,
                'user' => $this->submission->user,
                'category' => $this->submission->category,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
