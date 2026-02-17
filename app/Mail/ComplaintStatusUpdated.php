<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;

class ComplaintStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public Complaint $complaint;
    public ?string $notes;

    public function __construct(Complaint $complaint, ?string $notes = null)
    {
        // categories bisa saja ga ada
        $relations = ['user', 'handler', 'applicant'];
        if (Schema::hasTable('categories')) {
            $relations[] = 'category';
        }

        $this->complaint = $complaint->loadMissing($relations);
        $this->notes = $notes;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        $fromName    = config('mail.from.name');

        if (!$fromAddress) {
            throw new \RuntimeException('MAIL_FROM_ADDRESS belum diset di env/config.');
        }

        $creator  = $this->complaint->user;
        $userType = $creator->user_type ?? null;

        $pemohon = ($userType === 'pegawai')
            ? ($this->complaint->applicant ?? null)
            : $creator;

        $toEmail = $pemohon->email ?? null;
        $toName  = $pemohon->nama_lengkap ?? $pemohon->name ?? null;

        if ($toEmail) {
            $this->to($toEmail, $toName);
        }

        return $this
            ->from($fromAddress, $fromName)
            ->subject('Update Status Pengaduan #' . ($this->complaint->ticket_number ?? $this->complaint->id))
            ->view('emails.complaint_status_updated')
            ->with([
                'complaint' => $this->complaint,
                'user'      => $pemohon, // ✅ pemohon sebenarnya (applicant kalau pegawai)
                'category'  => (Schema::hasTable('categories') ? ($this->complaint->category ?? null) : null),
                'handler'   => $this->complaint->handler,
                'notes'     => $this->notes,
            ]);
    }
}