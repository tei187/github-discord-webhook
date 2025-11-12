<?php

namespace tei187\GitDisWebhook\Services\GitHub;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Services\Abstract\ServiceAbstract;
use tei187\GitDisWebhook\Factories\GitHubPayloadFactory;
use tei187\GitDisWebhook\Factories\GitHubMessageFactory;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\MessageInterface;

/**
 * Service class for handling GitHub webhook requests.
 * 
 * The GitHubService class extends the abstract ServiceAbstract class and provides
 * specific implementations for validating GitHub webhook payloads and signatures.
 * It also manages GitHub-specific configurations such as event types and repository information.
 * 
 * @package tei187\GitDisWebhook\Services\GitHub
 */
class GitHubService extends ServiceAbstract {
    protected string $name = 'github';
    protected ?string $payloadFactoryClass = GitHubPayloadFactory::class;
    protected ?string $messageFactoryClass = GitHubMessageFactory::class;
    
    /**
     * @var array List of repositories configured for the GitHub service. Referenced from config.
     */
    protected array $repositories = [];

    /**
     * @var string The event type for the GitHub webhook.
     */
    protected string $event;
    /**
     * @var string The secret used for validating GitHub webhook signatures.
     */
    protected string $secret;
    /**
     * @var array Messages configuration for GitHub service.
     */
    protected array $messages;

    /**
     * Constructs a new GitHubService instance.
     *
     * @param string $payload The raw webhook payload string.
     * @throws \InvalidArgumentException if the payload is invalid.
     */
    public function __construct(?Config $config = null, ?PayloadInterface $payload = null, ?WebhookInterface $webhook = null, ?MessageInterface $message = null) {
        parent::__construct($config, $payload, $webhook, $message);

        if (!$this->validatePayload($payload)) {
            throw new \InvalidArgumentException('Payload could not be validated.');
        }

        // instantiate payload factory
        $this->payloadFactory = new $this->payloadFactoryClass($this->config);

        // reference repositories from config
        $this->repositories = $this->config->services['github']['repositories'] ?? [];
        // reference messages from config
        $this->messages = $this->config->messages->github ?? [];

        // set event and secret
        $this->event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
        $this->secret = $this->config->secret ?? '';
    }

    /**
     * Validates the payload to ensure it is a valid JSON string.
     *
     * @param string|null $payload The payload string to validate. If null, uses the current payload.
     * @return bool True if the payload is valid JSON, false otherwise.
     */
    public function validatePayload(?string $payload = null): bool {
        $payload = $payload ?? $this->payload->plain;
        return \tei187\GitDisWebhook\Helpers\Validator::isJsonString($payload);
    }

    /**
     * Validates the signature of the payload using HMAC with SHA-1 or SHA-256.
     *
     * @param string $signature The signature to validate.
     * @return bool True if the signature is valid, false otherwise.
     */
    public function validateSignature(string $signature): bool {
        $signature256 = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';

        if ($signature256) {
            $expected = 'sha256=' . hash_hmac('sha256', $this->payload->plain, $this->secret);
            return hash_equals($expected, $signature256);
        } elseif ($signature) {
            $expected = 'sha1=' . hash_hmac('sha1', $this->payload->plain, $this->secret);
            return hash_equals($expected, $signature);
        }
        return false;
    }
    
    //
    // GitHub specific methods
    //

        /**
         * Gets the event type from the HTTP headers.
         *
         * @return string The event type.
         */
        public function getEventType(): string {
            return $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
        }

        /**
         * Sets the event type.
         *
         * @param string $event The event type to set.
         * @return self
         */
        public function setEventType(string $event): self {
            $this->event = $event;
            return $this;
        }

        /**
         * Gets the repository name from the payload.
         *
         * @return string The repository name.
         */
        public function getRepositoryName(): string {
            return $this->payload->parsed['repository']['full_name'] ?? '';
        }
}
