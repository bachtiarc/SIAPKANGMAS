<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BrevoMailer;

class ContactController extends Controller
{
    public function send(Request $request, \App\Services\BrevoMailer $brevo)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['required','email','max:255'],
            'subject' => ['nullable','string','max:255'],
            'message' => ['required','string'],
        ]);

        $toEmail = 'siapkangmasdisperindag@gmail.com';
        $toName  = 'Admin SIAPKANGMAS';

        $subject = '[Kontak] ' . ($data['subject'] ?? 'Pesan Baru');

        $html = nl2br(e(
            "Nama: {$data['name']}\n" .
            "Email: {$data['email']}\n" .
            "Subject: " . ($data['subject'] ?? '-') . "\n\n" .
            "Pesan:\n{$data['message']}"
        ));

        $brevo->sendTransactional(
            toEmail: $toEmail,
            toName: $toName,
            subject: $subject,
            htmlContent: $html,
            replyToEmail: $data['email'],
            replyToName: $data['name']
        );

        return back()->with('success', 'Pesan berhasil dikirim!');
    }
}