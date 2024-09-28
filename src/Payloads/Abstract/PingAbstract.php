<?php

namespace  tei187\GithubDiscordWebhook\Payloads\Abstract;

use tei187\GithubDiscordWebhook\Traits\PayloadUsesSender;

abstract class PingAbstract extends PayloadAbstract {
    use PayloadUsesSender;

    // assigned
    protected  string $event   = 'ping';
    protected ?string $subject = null;
    protected ?string $action  = null;
}