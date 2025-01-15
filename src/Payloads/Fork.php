<?php

namespace tei187\GitDisWebhook\Payloads;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Payloads\Abstract\ForkAbstract;

/**
 * This class extends `ForkAbstract` and provides a method to parse a payload and return a `Fork` object.
 */
class Fork extends ForkAbstract {
    public function parse(string $payload): self {
        $decoded = json_decode($payload);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain  = (string) $payload;
            $this->repo   = (object) self::makeRepo($decoded);
            $this->forkee = (object) self::makeForkee($decoded);

            return $this;
        }

        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
