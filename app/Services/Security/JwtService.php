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

    public function verify(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        $headerJson = $this->base64UrlDecode($headerEncoded);
        $payloadJson = $this->base64UrlDecode($payloadEncoded);
        $signature = $this->base64UrlDecode($signatureEncoded);

        if ($headerJson === null || $payloadJson === null || $signature === null) {
            return null;
        }

        $header = json_decode($headerJson, true);
        $claims = json_decode($payloadJson, true);

        if (! is_array($header) || ! is_array($claims)) {
            return null;
        }

        if (($header['alg'] ?? null) !== 'HS256') {
            return null;
        }

        $expectedSignature = hash_hmac('sha256', $headerEncoded.'.'.$payloadEncoded, $this->getSecret(), true);

        if (! hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $now = time();

        if (isset($claims['nbf']) && is_numeric($claims['nbf']) && $now < (int) $claims['nbf']) {
            return null;
        }

        if (isset($claims['exp']) && is_numeric($claims['exp']) && $now >= (int) $claims['exp']) {
            return null;
        }

        return $claims;
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

    private function base64UrlDecode(string $value): ?string
    {
        $remainder = strlen($value) % 4;

        if ($remainder !== 0) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}
