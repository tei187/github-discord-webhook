<?php

namespace tei187\GithubDiscordWebhook\Interfaces;

interface Payload {
    public function parse(string $payload): self;
}