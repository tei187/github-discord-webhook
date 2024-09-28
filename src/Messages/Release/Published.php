<?php
namespace tei187\GithubDiscordWebhook\Messages\Release;

use tei187\GithubDiscordWebhook\Messages\MessageAbstract;

class Published extends MessageAbstract {
    /**
     * Generates a message for a release event on a GitHub repository.
     *
     * The message includes information about the repository, branch, pusher, and a list of the new commits.
     * 
     * @return void
     */
    protected function create(): void {
        $message = "New release to **[{$this->webhook->payload->repo->fullname}](https://github.com/{$this->webhook->payload->repo->fullname})**"
                 . " by *[{$this->webhook->payload->author->name}]({$this->webhook->payload->author->url})*\n"
                 . "({$this->webhook->payload->release->tag}) **{$this->webhook->payload->release->name}**"
                 . ( $this->webhook->payload->release->prerelease ? "*(pre-release)*\n" : "\n" )
                 . "**Description**\n"
                 . "{$this->webhook->payload->release->desc}\n";

        $this->message = $message;
    }
}