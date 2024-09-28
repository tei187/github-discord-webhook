<?php

namespace tei187\GithubDiscordWebhook\Payloads\Push;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Payloads\Abstract\BranchAbstract;

class Branch extends BranchAbstract {
    public function parse(string $payload): self {
        $decoded = json_decode($payload);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain  = (string) $payload;
            $this->repo   = (object) self::makeRepo($decoded);
            $this->action = (string) self::makeAction($decoded);
            $this->branch = (string) self::makeBranch($decoded);
            $this->pusher = (object) self::makePusher($decoded);
            $this->sender = (object) self::makeSender($decoded);
            $this->checkAllowed();
            
            return $this;
        }

        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}