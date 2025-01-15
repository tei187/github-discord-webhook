<?php

namespace tei187\GitDisWebhook\Traits;

/**
 * Provides functionality for working with the fork information from a GitHub webhook payload.
 */
trait PayloadUsesFork {
    /**
     * @var \stdClass An object based on sender information from event triggered.
     *                If the payload is parsed, it will have the following properties: `name`, `url`, `avatar`, `type`.
     */
    protected object $forkee;

    /**
     * Creates a new `\stdClass` object representing the details of the entity who sent the commits associated with the GitHub webhook request.
     *
     * @param object $decoded The decoded JSON payload of the GitHub webhook request.
     * @return \stdClass An object with the following properties: `name`, `url`, `avatar`, `type`.
     */
    protected static function makeForkee(object $decoded): \stdClass {
        $forkee = new \stdClass;
        $forkee->avatar = (string) $decoded->forkee->owner->avatar_url;
        $forkee->name   = (string) $decoded->forkee->owner->login;
        $forkee->type   = (string) $decoded->forkee->owner->type;
        $forkee->url    = (string) $decoded->forkee->owner->html_url;

        return $forkee;
    }
}
