<?php

namespace tei187\GitDisWebhook\Messages;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Messages\MessageAbstract;

class Ping extends MessageAbstract {
    /**
     * Creates a message indicating that a ping was received from the specified repository.
     * 
     * @return void
     */
    public function create(): void {
        $this->message = "Ping received from **[{$this->webhook->payload->repo->fullname}](https://github.com/{$this->webhook->payload->repo->fullname})**";
    }
    
    public function successResponse(): ResponseHandler {
        ResponseHandler::send('Pong!', 'success', 200);
    }
}