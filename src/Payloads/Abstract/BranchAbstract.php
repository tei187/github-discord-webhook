<?php

namespace tei187\GithubDiscordWebhook\Payloads\Abstract;

use tei187\GithubDiscordWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GithubDiscordWebhook\Traits\PayloadUsesBranch;
use tei187\GithubDiscordWebhook\Traits\PayloadUsesPusher;
use tei187\GithubDiscordWebhook\Traits\PayloadUsesSender;

abstract class BranchAbstract extends PayloadAbstract {
    use PayloadUsesBranch,
        PayloadUsesPusher,
        PayloadUsesSender;
    
    // assigned
    protected  string $event   = 'push';
    protected ?string $subject = 'branch';

    /**
     * Checks the action performed on the branch.
     *
     * @param object $decoded The decoded JSON payload.
     * @return self
     */
    public function makeAction(object $decoded): ?string {
        $action = $decoded->created ? 'created' : null;
        $action = $decoded->deleted ? 'deleted' : $action;

        return $action;
    }
}