<?php

namespace tei187\GitDisWebhook\Platform\Github\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\GitHubPayloadAbstract;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesPusher;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo;
use tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender;

/**
 * Represents an abstract class for handling tag-related payloads in a Git-based webhook.
 * This class extends the `PayloadAbstract`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesPusher
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesSender
 * @uses \tei187\GitDisWebhook\Platform\Github\Traits\PayloadUsesRepo
 */
abstract class TagAbstract extends GitHubPayloadAbstract {
    use PayloadUsesPusher,
        PayloadUsesRepo,
        PayloadUsesSender;

    // designation
        protected  string $event   = 'push';
        protected ?string $subject = 'tag';

    protected ?string $tagName;

    /**
     * Checks the tag name from the decoded payload data.
     *
     * @param object $decoded The decoded payload data.
     * @return $string
     */
    protected static function makeTagName(object $decoded): string {
        return (string) explode("/", $decoded->ref)[2];
    }

    /**
     * Checks the action performed on the tag.
     *
     * @param object $decoded The decoded payload data.
     * @return ?string
     */
    protected static function makeAction(object $decoded): ?string {
        $action = $decoded->created ? (string) 'created' : null;
        $action = $decoded->deleted ? (string) 'deleted' : $action;
        return $action;
    }

    abstract protected function parse(string $payload): void;
}