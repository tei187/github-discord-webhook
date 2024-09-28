<?php

namespace tei187\GithubDiscordWebhook\Payloads\Abstract;

use tei187\GithubDiscordWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GithubDiscordWebhook\Traits\PayloadUsesBranch;
use tei187\GithubDiscordWebhook\Traits\PayloadUsesPusher;
use tei187\GithubDiscordWebhook\Traits\PayloadUsesSender;

abstract class CommitAbstract extends PayloadAbstract {
    use PayloadUsesPusher,
        PayloadUsesSender,
        PayloadUsesBranch;

    /**
     * @var array The commits associated with the GitHub webhook request.
     */
    protected array $commits;

    /**
     * @var bool Indicates whether the push was a forced push.
     */
    protected bool $forced;

    // assigned
    protected  string $event   = 'push';
    protected ?string $subject = 'commit';
    protected ?string $action  = null;
}