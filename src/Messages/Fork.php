<?php

namespace tei187\GitDisWebhook\Messages;

use tei187\GitDisWebhook\Messages\MessageAbstract;

/**
 * This class extends `MessageAbstract` and provides a method to create message for `fork` event.
 */
class Fork extends MessageAbstract {
    /**
     * Creates a message indicating that a fork event was received from the specified repository.
     * 
     * @return void
     */
    protected function create(): void {
        $this->message = "Repository **[{$this->webhook->payload->repo->fullname}]({$this->webhook->payload->repo->url})** "
                       . "forked by **[{$this->webhook->payload->forkee->name}](https://github.com/{$this->webhook->payload->forkee->name})**.";
    }
}