<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoMailer
{
    public function sendTransactional(
        string $toEmail,
        ?string $toName,
        string $subject,
        string $htmlContent
    ): array {
        $apiKey = config('brevo.api_key');
        if (!$apiKey) {
            throw new \RuntimeException('BREVO_API_KEY belum diset (config/brevo.php)');
        }

        $fromEmail = config('mail.from.address');
        $fromName  = config('mail.from.name', config('app.name'));

        if (!$fromEmail) {
            throw new \RuntimeException('MAIL_FROM_ADDRESS belum diset');
        }

        $payload = [
            'sender' => [
                'name'  => $fromName,
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

        /** @var Response $res */
        $res = Http::withHeaders([
            'api-key' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        Log::info('Brevo API response', [
            'status' => $res->status(),
            'body' => $res->body(),
        ]);

        if (!$res->successful()) {
            throw new \RuntimeException("Brevo API error {$res->status()}: {$res->body()}");
        }

        return $res->json() ?? ['raw' => $res->body()];
    }
}
