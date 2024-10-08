<?php

namespace tei187\GitDisWebhook\Payloads\Push;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Payloads\Abstract\CommitAbstract;

class Commit extends CommitAbstract {
    public function parse(string $payload): self {
        $decoded = json_decode($payload);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain   = (string) $payload;
            $this->commits = (array)  $decoded->commits;
            $this->forced  = (bool)   $decoded->forced;
            $this->branch  = (string) self::makeBranch($decoded);
            $this->pusher  = (object) self::makePusher($decoded);
            $this->repo    = (object) self::makeRepo($decoded);

            return $this;
        }
        
        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
