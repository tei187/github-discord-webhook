<?php

namespace tei187\GitDisWebhook\Platform\Github\Messages\Push\Tag;

use tei187\GitDisWebhook\Messages\MessageAbstract;

/**
 * This class extends `MessageAbstract` and provides a method to create message for `push.tag.created` event.
 */
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