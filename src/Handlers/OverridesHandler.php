<?php

namespace tei187\GitDisWebhook\Handlers;

use tei187\GitDisWebhook\Enums\ConfigKeys;
use tei187\GitDisWebhook\Enums\OverrideType;
use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Interfaces\Webhook as WebhookInterface;

/**
 * Handles overrides for webhook payloads.
 * 
 * This class is responsible for managing and applying overrides to webhook payloads.
 * It works with different types of overrides as defined in the OverrideType enum.
 * 
 * @see tei187\GitDisWebhook\Enums\OverrideType
 */
class OverridesHandler {
    /**
     * Handles the application of overrides to a webhook payload, namely used message classes and allowed states.
     *
     * @param  WebhookInterface $webhook The type of event being handled.
     * @param  ?array           $config  The configuration data for the event, or `null` to load it from webhook config.
     * @param  ?OverrideType    $type    The type of override to apply, or null if no specific type.
     * 
     * @return array|bool|string|null Array, bool, string (depending on the type of override and path factors) or `null` if not found.
     */
    public static function handle(WebhookInterface $webhook, ?array $config = null, ?OverrideType $type = null) {
        $config = $config ?? ConfigHandler::load(ConfigKeys::WEBHOOKS);
        $overrides = self::load($webhook, $config);

        if ($overrides === null) {
            return null;
        }

        $path = match($type) {
            OverrideType::MESSAGES => 'messages.' . $webhook->payload->getDottedPath(),
            OverrideType::ALLOWED  => 'allowed.'  . $webhook->payload->getDottedPath(),
            default                => $webhook->payload->getDottedPath(),
        };

        return ArrayHandler::getValueByDotNotation($overrides, $path);
    }

    /**
     * Checks if the event has overrides defined in the configuration.
     *
     * @param  WebhookInterface $webhook The type of event being handled.
     * @param  array            $config  The configuration data for the event.
     * 
     * @return array|null Array of overrides or `null` if no overrides are defined.
     */
    public static function load(WebhookInterface $webhook, ?array $config = null): ?array {
        $config = $config ?? ConfigHandler::load(ConfigKeys::WEBHOOKS);
        return 
            key_exists($webhook->name, $config) 
                ? ( key_exists('overrides', $config[$webhook->name]) 
                        ? $config[$webhook->name]['overrides']
                        : null )
                : null;
    }
}
