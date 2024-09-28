<?php

namespace tei187\GithubDiscordWebhook\Interfaces;

use tei187\GithubDiscordWebhook\Interfaces\Payload as PayloadInterface;

interface Webhook {
    public function validateProfile(): bool;
    public function validateSignature(): bool;
    public function setPayload(PayloadInterface $payload);
}