<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\MessageInterface;
use tei187\GitDisWebhook\ValueObjects\Config;

/**
 * Provides a factory for creating message objects based on the provided event and webhook data.
 *
 * The MessageFactory class is responsible for creating the appropriate message object based on the * event and webhook data.
 * It checks for any overrides in the webhook data and uses the configuration to determine the correct message class to instantiate.
 *
 * @package tei187\GitDisWebhook\Factories
 */
interface MessageFactoryInterface
{
    /**
     * Constructs a new MessageFactory instance.
     *
     * @param Config|null $config The configuration for the message classes.
     * @param string|null $serviceName Optionally stores the service name for message resolution.
     */
    public function __construct(?Config $config = null, ?string $serviceName = null);

    /**
     * Creates a message object based on the provided event.
     *
     * @param string           $event   The event that triggered the message.
     * @param PayloadInterface $payload The payload data of Payload interface.
     * @param WebhookInterface $webhook The webhook data of Webhook interface.
     * @return MessageInterface|null The message object of Message interface, or ResponseHandler void.
     */
    public function createMessage($event, PayloadInterface $payload, WebhookInterface $webhook): ?MessageInterface;
}