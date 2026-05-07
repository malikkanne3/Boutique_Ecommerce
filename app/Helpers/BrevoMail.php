<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Log;

class BrevoMail
{
    public static function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $apiKey = env('BREVO_API_KEY');

        if (empty($apiKey)) {
            Log::error('BrevoMail: BREVO_API_KEY est vide !');
            return false;
        }

        $data = [
            'sender'      => ['name' => env('MAIL_FROM_NAME', 'E-Shop SN'), 'email' => env('MAIL_FROM_ADDRESS', 'eshopsn7@gmail.com')],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ];

        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'api-key: ' . $apiKey,
            'content-type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        Log::info('BrevoMail response', [
            'to'       => $toEmail,
            'subject'  => $subject,
            'httpCode' => $httpCode,
            'response' => $response,
            'error'    => $curlError,
        ]);

        return $httpCode === 201;
    }
}