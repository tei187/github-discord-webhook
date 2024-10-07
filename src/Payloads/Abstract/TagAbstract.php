<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Traits\PayloadUsesPusher;
use tei187\GitDisWebhook\Traits\PayloadUsesSender;

abstract class TagAbstract extends PayloadAbstract {
    use PayloadUsesPusher,
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
    static function makeTagName(object $decoded): string {
        return (string) explode("/", $decoded->ref)[2];;
    }

    /**
     * Checks the action performed on the tag.
     *
     * @param object $decoded The decoded payload data.
     * @return ?string
     */
    static function makeAction(object $decoded): ?string {
        $action = $decoded->created ? (string) 'created' : null;
        $action = $decoded->deleted ? (string) 'deleted' : $action;
        return $action;
    }
}