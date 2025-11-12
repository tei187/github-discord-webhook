<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Factories\MessageFactory;

/**
 * Provides a factory for creating message objects based on the provided event and webhook data for GitHub.
 *
 * The GitHubMessageFactory class is responsible for creating the appropriate message object based on the * GitHub event and webhook data.
 * It checks for any overrides in the webhook data and uses the configuration to determine the correct message class to instantiate.
 *
 * @package tei187\GitDisWebhook\Factories
 */
class GitHubMessageFactory extends MessageFactory
{
    public function __construct(?Config $config = null, ?string $serviceName = 'github')
    {
        parent::__construct($config, $serviceName);
    }
}