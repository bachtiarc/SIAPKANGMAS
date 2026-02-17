<?php

namespace App\Mail;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;

class ConsultationStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Consultation $consultation;
    public ?string $note;

    public function __construct(Consultation $consultation, ?string $note = null)
    {
        $relations = ['user', 'handler', 'applicant'];
        if (Schema::hasTable('categories')) {
            $relations[] = 'category';
        }

        $this->consultation = $consultation->loadMissing($relations);
        $this->note = $note;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name');

        if (!$fromAddress) {
            throw new \RuntimeException('MAIL_FROM_ADDRESS belum diset di env/config.');
        }

        $creator  = $this->consultation->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($this->consultation->applicant ?? null)
            : $creator;

        $toEmail = $pemohon->email ?? null;
        $toName  = $pemohon->nama_lengkap ?? $pemohon->name ?? null;

        if ($toEmail) {
            $this->to($toEmail, $toName);
        }

        return $this
            ->from($fromAddress, $fromName)
            ->subject('Update Status Konsultasi #' . ($this->consultation->ticket_id ?? 'N/A'))
            ->view('emails.consultation_status_updated')
            ->with([
                'consultation' => $this->consultation,
                'user'         => $pemohon, // ✅ pemohon sebenarnya
                'category'     => (Schema::hasTable('categories') ? ($this->consultation->category ?? null) : null),
                'handler'      => $this->consultation->handler,
                'note'         => $this->note,
            ]);
    }
}