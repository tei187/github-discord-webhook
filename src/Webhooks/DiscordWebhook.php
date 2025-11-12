<?php

namespace tei187\GitDisWebhook\Webhooks;

use tei187\GitDisWebhook\Webhooks\Abstract\WebhookAbstract;

class DiscordWebhook extends WebhookAbstract {
    
    public function validateProfile(): bool {
        return true;
    }

    public function validateSignature(): bool {
        $hash = 'sha256=' . hash_hmac('sha256', $this->payload->getRawBody(), $this->secret);
        return hash_equals($hash, $this->payload->getHeader('X-Hub-Signature') ?? '');
    }

    public function send(array $payload): bool {
        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }
}
