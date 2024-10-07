<?php

namespace tei187\GitDisWebhook;

use tei187\GitDisWebhook\Enums\ConfigKeys;
use tei187\GitDisWebhook\Handlers\ConfigHandler;
use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Handlers\OverridesHandler;
use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Traits\UsesMagicGetter;
use tei187\GitDisWebhook\Interfaces\Webhook as WebhookInterface;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;

/**
 * Represents a profile for a Discord webhook associated with a GitHub repository.
 * The `Profile` class encapsulates the URL, GitHub secret, and list of repositories associated with a webhook.
 * It also provides methods for validating the webhook request signature and setting the request object.
 */
class Webhook implements WebhookInterface {
    use UsesMagicGetter;

    protected bool $validated = false;
    protected bool $allowed = false;

    /**
     * Constructs a new `Profile` instance with the given URL, secret, and repository list.
     *
     * @param string|null           $name      The name of the webhook profile.
     * @param string|null           $url       The URL of Discord webhook associated with the profile.
     * @param string|null           $secret    The GitHub secret associated with the profile.
     * @param array|null            $repos     The list of repositories associated with the profile.
     * @param PayloadInterface|null $payload   The payload object used with the profile.
     * @param array|null            $overrides Array of overrides to allowed payloads and messages.
     *                                         If `null`, the profile will use configured overrides by default.
     * @param bool                  $validated Whether the profile has been validated.
     * @param bool|null             $allowed   Whether the profile is allowed to be processed in messaging. 
     *                                         It can be `null` at some point, if event path is not found in webhook's overrides,
     *                                         at which point it will check the default configuration.
     */
    final function __construct(
        protected ?string $name = null,
        protected ?string $url = null,
        protected ?string $secret = null,
        protected ?array  $repos = [],
        protected ?PayloadInterface $payload = null,
        protected ?array  $overrides = null,
    ) {

        // set payload
        $this->setPayload($payload);

        // set repos
        $this->repos = is_array($this->repos) 
            ? array_map('strtolower', $this->repos) 
            : [];

        // validate profile
        $this->validated = $this->validateProfile() ?: false;

        // set overrides
        $this->overrides = is_array($overrides) && !empty($overrides)
            ? $overrides
            : OverridesHandler::load($this);

        // validate event allowed
        $this->allowed = $this->validateEvent();
    }

    /**
     * Sets the payload object associated with the profile.
     * Enforces signature validation of the payload instance. If the signature is invalid, an error response is sent back to repository.
     *
     * @param PayloadInterface $payload The request object to set.
     * @return $this The current instance of the Webhook class profile.
     */
    final public function setPayload(PayloadInterface $payload): self {
        $this->payload = $payload;

        if($this->validateSignature() === false) {
            ResponseHandler::send("Invalid payload signature.", "error", 401);
        }

        return $this;
    }

    /**
     * Validates the profile by ensuring the URL, secret, and repositories are not null or empty.
     *
     * @return true|void True if the profile is valid, otherwise ReponseHandler void.
     */
    final public function validateProfile(): bool {
        match(null) {
            $this->url    => ResponseHandler::send("Webhook not configured properly. Discord URL is required.", "error", 500),
            $this->secret => ResponseHandler::send("Webhook not configured properly. Secret is required.", "error", 500),
            default => null
        };
        return true;
    }

    /**
     * Validates the signature of the incoming webhook request by comparing the
     * calculated HMAC signature with the signature provided in the request headers.
     *
     * @return bool|void True if the signature is valid, false otherwise.
     */
    final public function validateSignature(): bool {
        // check if repo is allowed to be handled by webhook profile
        if(!empty($this->repos)) {
            if(!in_array($this->payload->repo->fullname, $this->repos)) {
                ResponseHandler::send("This webhook is not configured to handle calls from this repository.", "error", 406);
            }
        }

        // Get the GitHub signature from the headers
        $githubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';

        // Calculate our signature
        $calculatedSignature = 'sha1=' . hash_hmac('sha1', $this->payload->plain, $this->secret);

        // Compare signatures
        return hash_equals($calculatedSignature, $githubSignature);
    }

    /**
     * Validates the event payload to ensure it is allowed to be handled by the webhook profile.
     * If the payload event path is not allowed, an error response is sent back to the repository.
     *
     * @return bool|void True if the event payload is allowed, false otherwise.
     */
    final public function validateEvent(): bool {
        $path = $this->payload->getDottedPath();

        // check if override is set
        $allowed = is_array($this->overrides) && key_exists('allowed', $this->overrides)
            ? ArrayHandler::getValueByDotNotation($this->overrides['allowed'], $path)
            : null;

        // check allowed state
        if(is_bool($allowed) && !$allowed) {
            ResponseHandler::send("This webhook does not allow handling of this payload or event path. Check configuration.", "error", 406);
            return false;
        } elseif(is_null($allowed)) {
            // if path is not found in overrides, check if it's allowed by default
            $config  = ConfigHandler::load(ConfigKeys::ALLOWED);
            $allowed = ArrayHandler::getValueByDotNotation($config, $path);

            // if path is still not found in config, then assume it's not allowed
            if($allowed === null) {
                ResponseHandler::send("This webhook does is not configured to handle this payload or event. Check configuration.", "error", 406);
                return false;
            }
        }

        return $allowed;
    }
}
