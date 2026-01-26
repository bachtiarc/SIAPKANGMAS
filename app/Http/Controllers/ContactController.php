<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'subject' => ['nullable','string','max:255'],
            'message' => ['required','string'],
        ]);

        $to = config('mail.from.address'); // atau email tujuan admin
        // contoh: $to = 'siapkangmasdisperindag@gmail.com';

        Mail::raw(
            "Nama: {$data['name']}\nEmail: {$data['email']}\nSubject: ".($data['subject'] ?? '-')."\n\nPesan:\n{$data['message']}",
            function ($mail) use ($data, $to) {
                $mail->to($to)
                     ->subject('[Kontak] ' . ($data['subject'] ?? 'Pesan Baru'))
                     ->replyTo($data['email'], $data['name']);
            }
        );

        return back()->with('success', 'Pesan berhasil dikirim!');
    }
}