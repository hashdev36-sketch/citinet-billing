<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->baseUrl = config('services.paystack.payment_url', 'https://api.paystack.co');
    }

    /**
     * Initialize a transaction. Returns the authorization_url to redirect the customer to.
     */
    public function initialize(string $email, int $amountKobo, string $reference, array $metadata = []): array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $email,
                'amount' => $amountKobo, // Paystack expects the smallest currency unit (kobo)
                'reference' => $reference,
                'callback_url' => route('checkout.callback'),
                'metadata' => $metadata,
            ])
            ->throw()
            ->json();

        return $response['data'];
    }

    /**
     * Verify a transaction server-side against Paystack's API.
     *
     * This is the ONLY source of truth for payment success. The frontend callback
     * page is never trusted on its own — it just redirects here to confirm.
     */
    public function verify(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/" . rawurlencode($reference))
            ->throw()
            ->json();

        return $response['data'];
    }

    /**
     * Validate that a webhook payload actually came from Paystack, using the
     * X-Paystack-Signature header (HMAC SHA512 of the raw body with the secret key).
     */
    public function isValidWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        if (! $signatureHeader) {
            Log::warning('Paystack webhook received with no signature header.');
            return false;
        }

        $expected = hash_hmac('sha512', $rawBody, $this->secretKey);

        return hash_equals($expected, $signatureHeader);
    }
}
