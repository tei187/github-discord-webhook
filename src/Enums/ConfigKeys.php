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
    case CONFIG   = 'config';
    case MESSAGES = 'messages';
    case WEBHOOKS = 'webhooks';
}