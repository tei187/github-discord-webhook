<?php

namespace tei187\GitDisWebhook\Handlers;

use tei187\GitDisWebhook\Enums\ConfigKeys;

/**
 * Handles configuration loading.
 * 
 * @see tei187\GitDisWebhook\Enums\ConfigKeys
 */
class ConfigHandler {
    /**
     * Loads the configuration from the specified file path.
     *
     * It will use ConfigKeys enum or name of the PHP file associated with the config. If null is set, it will load all configs into single array.
     *
     * @param ConfigKeys|string|null $config The configuration to load.
     * @return array|null The loaded configuration, or null if the configuration could not be loaded.
     */
    static public function load(ConfigKeys|string|null $config): ?array {
        if(is_string($config)) {
            $config = basename($config);
        }

        return match($config) {
            // enum
            ConfigKeys::ALLOWED  => require(GHDWEBHOOK_ROOT . 'config/allowed.php'),
            ConfigKeys::CONFIG   => require(GHDWEBHOOK_ROOT . 'config/config.php'),
            ConfigKeys::MESSAGES => require(GHDWEBHOOK_ROOT . 'config/messages.php'),
            ConfigKeys::WEBHOOKS => require(GHDWEBHOOK_ROOT . 'config/webhooks.php'),
            // null will load all configs
            null => [ 'allowed'  => require(GHDWEBHOOK_ROOT . 'config/allowed.php'),
                      'messages' => require(GHDWEBHOOK_ROOT . 'config/messages.php'),
                      'payloads' => require(GHDWEBHOOK_ROOT . 'config/payloads.php'),
                      'webhooks' => require(GHDWEBHOOK_ROOT . 'config/webhooks.php') ],
            // default will check if config file exists and load it, or return `null`
            default => ( file_exists(GHDWEBHOOK_ROOT . "config/{$config}.php")
                           ? require(GHDWEBHOOK_ROOT . "config/{$config}.php")
                           : null )
        };
    }
}