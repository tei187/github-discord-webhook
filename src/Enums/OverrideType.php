<?php

namespace tei187\GitDisWebhook\Enums;

/**
 * Defines the types of overrides that can be applied to a webhook payload.
 * 
 * @see tei187\GitDisWebhook\Handlers\OverridesHandler
 */
enum OverrideType
{
    case ALLOWED;
    case MESSAGES;
}