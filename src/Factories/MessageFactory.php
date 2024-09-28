<?php
namespace tei187\GithubDiscordWebhook\Factories;

use tei187\GithubDiscordWebhook\Handlers\ArrayHandler;
use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Interfaces\Webhook as WebhookInterface;
use tei187\GithubDiscordWebhook\Interfaces\Message as MessageInterface;

class MessageFactory
{
    /**
     * Stores the configuration for the message classes.
     *
     * @var array
     */
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/messages.php';
    }

    /**
     * Creates a message object based on the provided event.
     *
     * @param string $event The event that triggered the message.
     * @param WebhookInterface $webhook The webhook data of Webhook interface.
     * @return MessageInterface|void The message object of Message interface, or ResponseHandler void.
     */
    public function createMessage($event, WebhookInterface $webhook)
    {
        $path = $webhook->payload->getDottedPath();

        $messageClass = ArrayHandler::getValueByDotNotation($this->config, $path);

        if($messageClass !== null) {
            return new $messageClass($webhook);
        }
        ResponseHandler::send("No matching message class found for event: $event", "error", 422);
    }
}