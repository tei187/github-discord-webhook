<?php
namespace tei187\GithubDiscordWebhook\Messages\Push\Tag;

use tei187\GithubDiscordWebhook\Messages\MessageAbstract;

class Created extends MessageAbstract {
    
    /**
     * Generates a message for a push event on a GitHub repository.
     *
     * The message includes information about the repository, branch, pusher, and a list of the new commits.
     * 
     * @return void
     */
    protected function create(): void {
        $this->message = "New tag **\"{$this->webhook->payload->tagName}\"** created in **[{$this->webhook->payload->repo->fullname}](https://github.com/{$this->webhook->payload->repo->fullname})**"
                       . " by *[{$this->webhook->payload->pusher->name}](https://github.com/{$this->webhook->payload->pusher->name})*.";
    }
}