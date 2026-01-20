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

    /**
     * Create a new message instance.
     */
    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('siapkangmasdisperindag@gmail.com', 'SIAPKANGMAS DISPERINDAG'),
            subject: 'Permohonan Informasi Berhasil - ' . $this->submission->ticket_id,
        );
    }

    /**
     * Get the message content definition.
     */
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

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}