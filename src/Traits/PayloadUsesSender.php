<?php

namespace tei187\GithubDiscordWebhook\Traits;

/**
 * Trait allowing handling of 'sender' data, if it exits in received payload.
 */
trait PayloadUsesSender {
    /**
     * @var \stdClass An object based on sender information from event triggered.
     *                If the payload is parsed, it will have the following properties: `login`, `url`, `avatar`, `type`.
     */
    protected object $sender;

    /**
     * Creates a new `\stdClass` object representing the details of the entity who sent the commits associated with the GitHub webhook request.
     *
     * @param object $decoded The decoded JSON payload of the GitHub webhook request.
     * @return \stdClass An object with the following properties: `name`, `url`, `avatar`, `type`.
     */
    static function makeSender(object $decoded): \stdClass {
        $sender = new \stdClass;
        $sender->name   = (string) $decoded->sender->login;
        $sender->url    = (string) $decoded->sender->html_url;
        $sender->avatar = (string) $decoded->sender->avatar_url;
        $sender->type   = (string) $decoded->sender->type;

        return $sender;
    }
}