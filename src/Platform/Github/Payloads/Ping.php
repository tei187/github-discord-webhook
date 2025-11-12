<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Platform\Github\Payloads\Abstract\PingAbstract;

/**
 * This class extends `PingAbstract` and provides a method to parse a payload and return a `Ping` object.
 */
class Ping extends PingAbstract {
    protected function parse(string $payload): void {
        $decoded = json_decode($payload);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain = (string) $payload;
            $this->repo  = (object) self::makeRepo($decoded);
            return;
        }

        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
