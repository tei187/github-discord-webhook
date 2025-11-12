<?php

namespace tei187\GitDisWebhook\Handlers;

use tei187\GitDisWebhook\Enums\ConfigKeys;

/**
 * Handles configuration loading.
 * 
 * @see tei187\GitDisWebhook\Enums\ConfigKeys
 * @package tei187\GitDisWebhook\Handlers
 */
class ConfigHandler {
    /**
     * Loads the configuration from the specified file path.
     *
     * It will use ConfigKeys enum or name of the PHP file associated with the config. If null is set, it will load all configs into single array.
     *
     * @param ConfigKeys|string|null $config The configuration to load. File associated with `ConfigKeys` enum, `null` for all default files (as
     *                                       collective array) or `string` being the filename of specific configuration PHP file within 'config`
     *                                       directory. Default `null`.
     * @param bool $asObject Whether to return the configuration as an object instead of an array. Default `false`.
     * @return array|null|object The loaded configuration as `array` (or as `object`, if flagged), or `null` if the configuration could not be loaded.
     */
    public static function load(ConfigKeys|string|null $config = null, bool $asObject = false): null|array|object {
        if(is_string($config)) {
            $config = basename($config);
        }

        $found = match($config) {
            // enum
            ConfigKeys::ALLOWED  => require(GHDWEBHOOK_ROOT . 'config/allowed.php'),
            //ConfigKeys::CONFIG   => require(GHDWEBHOOK_ROOT . 'config/config.php'),
            ConfigKeys::MESSAGES => require(GHDWEBHOOK_ROOT . 'config/messages.php'),
            ConfigKeys::PAYLOADS => require(GHDWEBHOOK_ROOT . 'config/payloads.php'),
            ConfigKeys::PROFILES => require(GHDWEBHOOK_ROOT . 'config/profiles.php'),
            ConfigKeys::SERVICES => require(GHDWEBHOOK_ROOT . 'config/services.php'),
            ConfigKeys::WEBHOOKS => require(GHDWEBHOOK_ROOT . 'config/webhooks.php'),
            ConfigKeys::PROFILES_DEFAULTS => require(GHDWEBHOOK_ROOT . 'config/profiles.default.php'),
            
            // null will load all configs
            null => [ 'allowed'  => require(GHDWEBHOOK_ROOT . 'config/allowed.php'),
                      'messages' => require(GHDWEBHOOK_ROOT . 'config/messages.php'),
                      'payloads' => require(GHDWEBHOOK_ROOT . 'config/payloads.php'),
                      'profiles' => require(GHDWEBHOOK_ROOT . 'config/profiles.php'),
                      'webhooks' => require(GHDWEBHOOK_ROOT . 'config/webhooks.php'),
                      'services' => require(GHDWEBHOOK_ROOT . 'config/services.php'),
                      'profiles_defaults' => require(GHDWEBHOOK_ROOT . 'config/profiles.default.php')
                    ],
            // default will check if config file exists and load it, or return `null`
            default => ( file_exists(GHDWEBHOOK_ROOT . "config/{$config}.php")
                           ? require(GHDWEBHOOK_ROOT . "config/{$config}.php")
                           : null )
        };

        if($asObject && is_array($found)) {
            return (object) $found;
        }

        return $found;
    }
}