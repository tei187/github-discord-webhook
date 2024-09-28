<?php
namespace tei187\GithubDiscordWebhook\Messages\Push\Branch;

use tei187\GithubDiscordWebhook\Messages\MessageAbstract;

class Created extends MessageAbstract {
    /**
     * Creates a message for a new branch creation event.
     * The message includes the name of the new branch, the repository it was created in, and the name of the user who created it.
     * 
     * @return void
     */
    protected function create(): void {
        $this->message = "New branch **\"{$this->webhook->payload->branch}\"** created in **[{$this->webhook->payload->repo->fullname}](https://github.com/{$this->webhook->payload->repo->fullname})**"
                       . " by *[{$this->webhook->payload->pusher->name}](https://github.com/{$this->webhook->payload->pusher->name})*.";
    }
}