<?php

namespace tei187\GithubDiscordWebhook\Messages;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Messages\MessageAbstract;

class Ping extends MessageAbstract {
    /**
     * Creates a message indicating that a ping was received from the specified repository.
     * 
     * @return void
     */
    public function create(): void {
        $this->message = "Ping received from **[{$this->webhook->request->repo->fullname}](https://github.com/{$this->webhook->request->repo->fullname})**";
    }
    
    public function successResponse() {
        ResponseHandler::send('Pong!', 'success', 200);
    }
}