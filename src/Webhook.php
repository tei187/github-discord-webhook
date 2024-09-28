<?php

namespace tei187\GithubDiscordWebhook;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Interfaces\Payload as PayloadInterface;

/**
 * Represents a profile for a Discord webhook associated with a GitHub repository.
 * The `Profile` class encapsulates the URL, GitHub secret, and list of repositories associated with a webhook.
 * It also provides methods for validating the webhook request signature and setting the request object.
 */
class Webhook implements \tei187\GithubDiscordWebhook\Interfaces\Webhook {
    /**
     * Constructs a new `Profile` instance with the given URL, secret, and repository list.
     *
     * @param string|null            $url     The URL of Discord webhook associated with the profile.
     * @param string|null            $secret  The GitHub secret associated with the profile.
     * @param array|null             $repos   The list of repositories associated with the profile.
     * @param PayloadInterface|null  $payload The request object associated with the profile.
     * @param bool|null              $validated Whether the profile has been validated.
     */
    final function __construct(
        private ?string $name = null,
        private ?string $url = null,
        private ?string $secret = null,
        private ?array  $repos = [],
        private ?PayloadInterface $payload = null,
        private ?bool $validated = false
    ) {
        if(is_array($this->repos)) {
            $this->repos = array_map('strtolower', $this->repos);
        } else {
            $this->repos = [];
        }

        $this->validated = $this->validateProfile() ?: false;

        if($this->request !== null) {
            $this->setPayload($payload);
        }
    }

    /**
     * Sets the request object associated with the profile.
     * Enforces signature validation of the payload instance. If the signature is invalid, an error response is sent back to repository.
     *
     * @param \tei187\GithubDiscordWebhook\Interfaces\Payload $request The request object to set.
     */
    final public function setPayload(PayloadInterface $payload) {
        $this->payload = $payload;

        if($this->validateSignature() === false) {
            ResponseHandler::send("Invalid payload signature", "error", 401);
        }
    }

    /**
     * Retrieves the value of the specified property from the `Profile` instance.
     *
     * @param string $param The name of the property to retrieve.
     * @return mixed The value of the specified property, or throws an `Exception` if the property is invalid.
     */
    function __get($param) {
        if(isset($this->$param)) {
            return match ($param) {
                'url'       => $this->url,
                'secret'    => $this->secret,
                'repos'     => $this->repos,
                'payload'   => $this->payload,
                'validated' => $this->validated,
                default     => null,
            };
        }
    }

    /**
     * Validates the profile by ensuring the URL, secret, and repositories are not null or empty.
     *
     * @return true|void True if the profile is valid, otherwise ReponseHandler void.
     */
    final public function validateProfile(): bool {
        if ($this->url === null) {
            ResponseHandler::send("Webhook not configured properly. Discord URL is required.", "error", 500);
        }
        if ($this->secret === null) {
            ResponseHandler::send("Webhook not configured properly. Secret is required.", "error", 500);
        }
        return true;
    }

    /**
     * Validates the signature of the incoming webhook request by comparing the
     * calculated HMAC signature with the signature provided in the request headers.
     *
     * @throws \Exception If repo name is not found in the repositories list for this profile.
     * @return bool|void True if the signature is valid, false otherwise.
     */
    final public function validateSignature(): bool {
        if(!empty($this->repos)) {
            if(!in_array($this->request->repo->fullname, $this->repos)) {
                ResponseHandler::send("This webhook is not configured to handle this repository.", "error", 422);
            }
        }

        return hash_equals(
            'sha1=' . hash_hmac('sha1', $this->request->plain, $this->secret),
            $_SERVER['HTTP_X_HUB_SIGNATURE']
        );
    }
}