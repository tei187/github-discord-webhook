<?php

namespace tei187\GitDisWebhook\Handlers;

use tei187\GitDisWebhook\Enums\ConfigKeys;
use tei187\GitDisWebhook\Enums\OverrideType;
use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\ValueObjects\Config;

/**
 * Handles overrides for webhook payloads.
 * 
 * This class is responsible for managing and applying overrides to webhook payloads.
 * It works with different types of overrides as defined in the OverrideType enum.
 * 
 * @see tei187\GitDisWebhook\Enums\OverrideType
 * @package tei187\GitDisWebhook\Handlers
 */
class OverridesHandler {
    /**
     * Handles the application of overrides to a webhook payload, namely used message classes and allowed states.
     *
     * @param  WebhookInterface $webhook The type of event being handled.
     * @param  ?Config          $config  The configuration data for the event, or `null` to load it from webhook config.
     * @param  ?OverrideType    $type    The type of override to apply, or null if no specific type.
     * 
     * @return array|bool|string|null Array, bool, string (depending on the type of override and path factors) or `null` if not found.
     */
    public static function handle(WebhookInterface $webhook, ?Config $config = null, ?OverrideType $type = null): array|bool|string|null {
        $config = $config ?? new Config();
        $overrides = self::load($webhook, $config);

        if ($overrides === null) {
            return null;
        }

        $path = match($type) {
            OverrideType::MESSAGES => 'messages.' . $webhook->payload->getDottedEventPath(),
            OverrideType::ALLOWED  => 'allowed.'  . $webhook->payload->getDottedEventPath(),
            default                => $webhook->payload->getDottedEventPath(),
        };

        return ArrayHandler::getValueByDotNotation($overrides, $path);
    }

    /**
     * Checks if the event has overrides defined in the configuration.
     *
     * @param  WebhookInterface $webhook The type of event being handled.
     * @param  ?Config          $config  The configuration data for the event.
     * 
     * @return array|null Array of overrides or `null` if no overrides are defined.
     */
    public static function load(WebhookInterface $webhook, ?Config $config = null): ?array {
        $config = $config ?? new Config();
        return 
            key_exists($webhook->name, $config->profiles) 
                ? ( key_exists('overrides', $config->profiles[$webhook->name]) 
                        ? $config->profiles[$webhook->name]['overrides']
                        : null )
                : null;
    }
}
