<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Interfaces\Webhook as WebhookInterface;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;
use tei187\GitDisWebhook\Interfaces\Message as MessageInterface;
use tei187\GitDisWebhook\Interfaces\MessageFactory as MessageFactoryInterface;
use tei187\GitDisWebhook\ValueObjects\Config;

/**
 * Provides a factory for creating message objects based on the provided event and webhook data.
 *
 * The MessageFactory class is responsible for creating the appropriate message object based on the * event and webhook data.
 * It checks for any overrides in the webhook data and uses the configuration to determine the correct message class to instantiate.
 *
 * @package tei187\GitDisWebhook\Factories
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * Stores the configuration for the message classes.
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Optionally stores the webhook for message resolution.
     *
     * @var WebhookInterface|null
     */
    protected ?WebhookInterface $webhook;

    /**
     * Optionally stores the service name for message resolution.
     *
     * @var string|null
     */
    protected ?string $serviceName = null;

    public function __construct(?Config $config = null, ?string $serviceName = null, ?WebhookInterface $webhook = null)
    {
        $this->config = $config ?? new Config();
        $this->serviceName = $serviceName;
        $this->webhook = $webhook;
    }

    /**
     * Creates a message object based on the provided event.
     *
     * @param string           $event   The event that triggered the message.
     * @param PayloadInterface $payload The payload data of Payload interface.
     * @param WebhookInterface|null $webhook The webhook data of Webhook interface.
     * @return MessageInterface|void The message object of Message interface, or ResponseHandler void.
     */
    public function createMessage($event, PayloadInterface $payload, ?WebhookInterface $webhook): MessageInterface
    {
        // get payload dotted path of the event
        $path = $payload->getDottedEventPath();

        // resolve message class from config
        if($this->serviceName !== null) {
            $path = $this->serviceName . '.' . $path;
        }
        $messageClass = ArrayHandler::getValueByDotNotation($this->config->messages, $path);

        if($messageClass !== null) {
            return new $messageClass($webhook);
        }
        
        ResponseHandler::send("No matching message class found for event: $event", "error", 422);
    }
}