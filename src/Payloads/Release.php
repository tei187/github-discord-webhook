<?php

namespace tei187\GitDisWebhook\Payloads;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Payloads\Abstract\ReleaseAbstract;

/**
 * This class extends `ReleaseAbstract` and provides a method to parse a payload and return a `Release` object.
 */
class Release extends ReleaseAbstract {
    /**
     * @var \stdClass Represents the release information extracted from the GitHub webhook payload. If
     *                parsed, will contain the following properties: `name`, `tag`, `desc`, `url`,
     *                `prerelease`.
     */
    protected object $release;
    
    /**
     * @var \stdClass Represents the author information extracted from the GitHub webhook payload. If 
     *                parsed, will contain the following properties: `name`, `url`.
     */
    protected object $author;

    public function parse(string $payload): self {
        $decoded = json_decode($payload);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain   = (string) $payload;
            $this->action  = (string) $decoded->action;
            $this->release = self::makeRelease($decoded);
            $this->author  = self::makeAuthor($decoded);
            $this->repo    = self::makeRepo($decoded);

            return $this;
        }
        
        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
