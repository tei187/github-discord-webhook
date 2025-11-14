<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Push;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Platform\Github\Payloads\Abstract\CommitAbstract;

/**
 * This class extends `CommitAbstract` and provides a method to parse a payload and return a `Commit` object.
 */
class Commit extends CommitAbstract {
    protected function parse(string $payload): void {
        $decoded = json_decode($payload);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain   = (string) $payload;
            $this->commits = (array)  $decoded->commits;
            $this->forced  = (bool)   $decoded->forced;
            $this->pusher  = (object) self::makePusher($decoded);
            $this->repo    = (object) self::makeRepo($decoded);
            return;
        }
        
        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
