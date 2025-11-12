<?php

namespace tei187\GitDisWebhook\Enums;

/**
 * Defines the configuration keys used throughout the application.
 * 
 * @see tei187\GitDisWebhook\Handlers\ConfigHandler
 */
enum ConfigKeys: string
{
    case ALLOWED  = 'allowed';
    //case CONFIG   = 'config';
    case MESSAGES = 'messages';
    case PAYLOADS = 'payloads';
    case PROFILES = 'profiles';
    case PROFILES_DEFAULTS = 'profiles_defaults';
    case SERVICES = 'services';
    case WEBHOOKS = 'webhooks';
}