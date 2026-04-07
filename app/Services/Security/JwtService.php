<?php

namespace App\Services\Security;

class JwtService
{
    public function generate(array $payload, ?int $ttlSeconds = null): array
    {
        $ttlSeconds ??= (int) config('jwt.ttl', 43200);
        $now = time();
        $exp = $now + $ttlSeconds;

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $claims = array_merge([
            'iss' => config('app.url', 'simpeg-api'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
        ], $payload);

        $headerEncoded = $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $payloadEncoded = $this->base64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES));

        $signature = hash_hmac('sha256', $headerEncoded.'.'.$payloadEncoded, $this->getSecret(), true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return [
            'token' => $headerEncoded.'.'.$payloadEncoded.'.'.$signatureEncoded,
            'expires_in' => $ttlSeconds,
        ];
    }

    private function getSecret(): string
    {
        $secret = config('jwt.secret');

        if (is_string($secret) && str_starts_with($secret, 'base64:')) {
            return (string) base64_decode(substr($secret, 7), true);
        }

        return (string) $secret;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
