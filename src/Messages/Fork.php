<?php

namespace tei187\GitDisWebhook\Messages;

use tei187\GitDisWebhook\Messages\MessageAbstract;

class Fork extends MessageAbstract {
    /**
     * Creates a message indicating that a fork event was received from the specified repository.
     * 
     * @return void
     */
    public function create(): void {
        $this->message = "Repository **[{$this->webhook->payload->repo->fullname}]({$this->webhook->payload->repo->url})** "
                       . "forked by **[{$this->webhook->payload->forkee->name}](https://github.com/{$this->webhook->payload->forkee->name})**.";
    }
}