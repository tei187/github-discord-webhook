<?php

namespace tei187\GithubDiscordWebhook\Payloads;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Payloads\Abstract\ReleaseAbstract;

/**
 * Represents a release event received from a GitHub webhook.
 * This class parses the JSON payload of the webhook and extracts relevant information such as the release info, sender details, and repository details.
 */
class Release extends ReleaseAbstract {
    /**
     * @var \stdClass Represents the release information extracted from the GitHub webhook payload. If parsed, will contain the following properties: `name`, `tag`, `desc`, `url`, `prerelease`.
     */
    protected object $release;
    
    /**
     * @var \stdClass Represents the author information extracted from the GitHub webhook payload. If parsed, will contain the following properties: `name`, `url`.
     */
    protected object $author;

    /**
     * Parses the provided JSON payload and extracts relevant information.
     *
     * @param string $payload The JSON payload to be parsed.
     * @throws \InvalidArgumentException If the provided payload is not valid JSON.
     * @return bool Returns true if the parsing was successful, false otherwise.
     */
    public function parse(string $payload): self {
        $decoded = json_decode($payload);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->plain   = (string) $payload;
            $this->action  = (string) $decoded->action;
            $this->release = self::makeRelease($decoded);
            $this->author  = self::makeAuthor($decoded);
            $this->repo    = self::makeRepo($decoded);
            $this->checkAllowed();

            return $this;
        }
        
        ResponseHandler::send("The provided payload is not valid JSON.", 'error', 422);
    }
}
