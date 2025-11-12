<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Push;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Platform\Github\Payloads\Abstract\BranchAbstract;

/**
 * This class extends `BranchAbstract` and provides a method to parse a payload and return a `Branch` object.
 */
class Branch extends BranchAbstract {
    protected function parse(string $payload): void {
        $decoded = json_decode($payload);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain  = (string) $payload;
            $this->repo   = (object) self::makeRepo($decoded);
            $this->action = (string) self::makeAction($decoded);
            $this->branch = (string) self::makeBranch($decoded);
            $this->pusher = (object) self::makePusher($decoded);
            $this->sender = (object) self::makeSender($decoded);
            return;
        }

        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}