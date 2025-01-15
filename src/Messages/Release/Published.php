<?php

namespace tei187\GitDisWebhook\Messages\Release;

use tei187\GitDisWebhook\Helpers\Markdown;
use tei187\GitDisWebhook\Messages\MessageAbstract;

/**
 * This class extends `MessageAbstract` and provides a method to create message for `release.published` event.
 */
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
                 . Markdown::newlineToQuote($this->webhook->payload->release->desc);

        $this->message = $message;
    }
}