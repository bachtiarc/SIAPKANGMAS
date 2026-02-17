<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;

class SubmissionStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Submission $submission;
    public ?string $note;

    public function __construct(Submission $submission, ?string $note = null)
    {
        // categories kadang tidak ada (kayak di controller kamu)
        $relations = ['user', 'handler', 'applicant'];
        if (Schema::hasTable('categories')) {
            $relations[] = 'category';
        }

        $this->submission = $submission->loadMissing($relations);
        $this->note = $note;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name');

        if (!$fromAddress) {
            throw new \RuntimeException('MAIL_FROM_ADDRESS belum diset di env/config.');
        }

        $creator  = $this->submission->user;
        $userType = $creator->user_type ?? null;

        $recipient = ($userType === 'pegawai' && $this->submission->applicant)
            ? $this->submission->applicant
            : $creator;

        $toEmail = $recipient->email ?? null;
        $toName  = $recipient->name ?? $recipient->nama_lengkap ?? null;

        if ($toEmail) {
            $this->to($toEmail, $toName);
        }

        return $this
            ->from($fromAddress, $fromName)
            ->subject('Update Status Tiket #' . ($this->submission->ticket_id ?? 'N/A'))
            ->view('emails.submission_status_updated')
            ->with([
                'submission' => $this->submission,
                'user'       => $recipient, // ✅ user di email = pemohon sebenarnya
                'category'   => (Schema::hasTable('categories') ? ($this->submission->category ?? null) : null),
                'handler'    => $this->submission->handler,
                'note'       => $this->note,
            ]);
    }
}
\