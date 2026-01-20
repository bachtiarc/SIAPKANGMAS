<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubmissionStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $note;

    public function __construct(Submission $submission, $note)
    {
        $this->submission = $submission;
        $this->note = $note;
    }

    public function build()
    {
        return $this->subject('Update Status Tiket #' . $this->submission->ticket_id)
                    ->view('emails.submission_status_updated');
    }
}