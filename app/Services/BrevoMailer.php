<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailer
{
    public function sendTransactional(
        string $toEmail,
        ?string $toName,
        string $subject,
        string $htmlContent
    ): void {
        $apiKey = env('BREVO_API_KEY');
        if (!$apiKey) {
            throw new \RuntimeException('BREVO_API_KEY belum diset di env.');
        }

        $fromEmail = env('MAIL_FROM_ADDRESS');
        if (!$fromEmail) {
            throw new \RuntimeException('MAIL_FROM_ADDRESS belum diset di env.');
        }

        $payload = [
            'sender' => [
                'name'  => env('MAIL_FROM_NAME', config('app.name')),
                'email' => $fromEmail,
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name'  => $toName ?: 'Pemohon',
                ],
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ];

        $res = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (!$res->successful()) {
            throw new \RuntimeException("Brevo API error {$res->status()}: {$res->body()}");
        }
    }
}