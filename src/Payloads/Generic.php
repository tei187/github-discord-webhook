<?php

namespace tei187\GitDisWebhook\Payloads;

use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;

/**
 * A generic payload class that implements the PayloadInterface.
 * 
 * Uses JSON as the base format for the payload.
 * Sets event path to an empty array by default.
 * 
 * @package tei187\GitDisWebhook\Payloads
 */
class GenericJsonBased extends PayloadAbstract {
    protected array $event;

    public function __construct(string $payload) {
        $this->plain = $payload;
    }

    protected function parse(string $payload): void {
        $decoded = json_decode($this->plain, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->parsed = $decoded;
            return;
        }

        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }

    public function getEventPath(): array {
        return ArrayHandler::filterNulls($this->event) ?? [];
    }
    
    public function setEvent(array $event): self {
        $this->event = $event;
        return $this;
    }
}