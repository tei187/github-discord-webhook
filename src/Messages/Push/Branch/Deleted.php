<?php

namespace tei187\GitDisWebhook\Messages\Push\Branch;

use tei187\GitDisWebhook\Messages\MessageAbstract;

/**
 * This class extends `MessageAbstract` and provides a method to create message for `push.branch.deleted` event.
 */
class Deleted extends MessageAbstract {
    /**
     * Creates a message for a branch deletion event.
     * The message includes the name of the new branch, the repository it was created in, and the name of the user who created it.
     * 
     * @return void
     */
    protected function create(): void {
        $this->message = "Branch **\"{$this->webhook->payload->branch}\"** deleted from **[{$this->webhook->payload->repo->fullname}](https://github.com/{$this->webhook->payload->repo->fullname})**"
                       . " by *[{$this->webhook->payload->pusher->name}](https://github.com/{$this->webhook->payload->pusher->name})*.";
    }
}