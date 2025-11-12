<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Handlers\GitHub\PayloadResolver as GitHubPayloadResolver;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;
use tei187\GitDisWebhook\Interfaces\PayloadFactory as PayloadFactoryInterface;

/**
 * Provides a factory for creating payload instances based on the provided event.
 *
 * The GitHubPayloadFactory is responsible for resolving the appropriate payload class for a given event and creating an instance of
 * that class with the provided payload data.
 * 
 * @package tei187\GitDisWebhook\Factories
 */
class GitHubPayloadFactory extends PayloadFactoryAbstract implements PayloadFactoryInterface
{
    public function createPayload(string $payload): PayloadInterface {
        $payloadClass = $this->resolvePayloadClass($payload, $this->config->payloads['github'] ?? []);

        return $payloadClass !== null
            ? new $payloadClass($payload)
            : ResponseHandler::send("No matching payload class found for event.\n ", "error", 422); 
    }

    protected function resolvePayloadClass(string $payload): ?string {
        return GitHubPayloadResolver::find($payload, $this->config->payloads['github'] ?? []);
    }
}