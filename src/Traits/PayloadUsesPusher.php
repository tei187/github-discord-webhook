<?php

namespace tei187\GithubDiscordWebhook\Traits;

/**
 * Trait allowing handling of 'pusher' data, if it exits in received payload.
 */
trait PayloadUsesPusher {
    /**
     * @var \stdClass An object based on sender information from event triggered.
     *                If the payload is parsed, it will have the following properties: `login`, `url`, 'email'.
     */
    protected object $pusher;

    /**
     * Creates a new `\stdClass` object representing the details of the user who pushed the commits associated with the GitHub webhook request.
     *
     * @param object $decoded The decoded JSON payload of the GitHub webhook request.
     * @return \stdClass An object with the following properties: `name`, `url`, 'email'.
     */
    static protected function makePusher(object $decoded): \stdClass {
        $pusher = new \stdClass();
        $pusher->name  = (string) $decoded->pusher->name;
        $pusher->url   = (string) "https://github.com/{$pusher->name}";
        $pusher->email = (string) $decoded->pusher->email;
        
        return $pusher;
    }
}