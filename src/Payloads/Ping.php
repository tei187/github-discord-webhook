<?php

namespace tei187\GithubDiscordWebhook\Payloads;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Payloads\Abstract\PingAbstract;

class Ping extends PingAbstract {
    public function parse(string $payload): self {
        $decoded = json_decode($payload);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain = (string) $payload;
            $this->repo  = (objecT) self::makeRepo($decoded);
            $this->checkAllowed();

            return $this;
        }

        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
