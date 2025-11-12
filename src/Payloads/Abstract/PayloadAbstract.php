<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Handlers\ConfigHandler;
use tei187\GitDisWebhook\Traits\UsesMagicGetter;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;


/**
 * Defines the base abstract class for handling webhook payloads.
 * This class provides common properties and methods for working with GitHub webhook payloads.
 * Implements `PayloadInterface`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Traits\UsesMagicGetter
 * 
 * @package tei187\GitDisWebhook\Payloads\Abstract
 */
abstract class PayloadAbstract implements PayloadInterface {
    use UsesMagicGetter;

    /**
     * @var string The raw JSON payload of the webhook request.
     */
    protected string $plain;

    /**
     * @var array|object The parsed data from the webhook payload.
     */
    protected array|object $parsed;

    /**
     * @var bool Indicates whether the payload is allowed to be processed in messaging.
     */
    protected bool $allowed;

    /**
     * @var string|null The origin of the payload, if applicable.
     */
    protected ?string $origin;

    /**
     * Constructs a new payload object with an optional JSON payload.
     *
     * @param string|null $payload The JSON payload to be parsed, or `null` if no payload is provided.
     */
    public function __construct( ?string $payload = null ) {
        $payload ? $this->setData($payload) : null;
        $this->setOrigin();
    }

    /**
     * Checks if the payload is allowed to be processed based on the configuration.
     *
     * This method filters the payload properties (event, subject, action) to create a dot-notation path, and then checks the
     * corresponding value in the payloads.php configuration file. If the value is a boolean, it is used to set the $allowed
     * property. Otherwise, $allowed is set to false.
     *
     * @return $this The current instance of the PayloadAbstract class.
     * @deprecated 1.1.0 Event validation is now handled by Webhook class, due to implementation of overrides configs.
     */
    final public function checkAllowed(): self {
        $config = ConfigHandler::load(\tei187\GitDisWebhook\Enums\ConfigKeys::ALLOWED);

        // path construction
        $path = array_filter( 
            [ $this->event, $this->subject, $this->action, ], 
            function($value) { return $value !== null && $value !== ''; }
        );
        $path = implode('.', array_map('trim', $path));

        // path check
        $check = ArrayHandler::getValueByDotNotation($config, $path);

        // assign outcome
        $this->allowed = is_bool($check) ? $check : false;

        return $this;
    }

    public function getHeader(string $header): ?string {
        // Try getallheaders() if available (Apache, Nginx, CLI, FastCGI, ...)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strcasecmp($key, $header) === 0) {
                    return $value;
                }
            }
        }
        
        // Fallback: look for HTTP_... in $_SERVER
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        // Special cases (CONTENT_TYPE, CONTENT_LENGTH, etc.)
        $special = strtoupper(str_replace('-', '_', $header));
        if (isset($_SERVER[$special])) {
            return $_SERVER[$special];
        }

        return null;
    }

    final public function getRawBody(): string {
        return $this->plain;
    }

    final public function getPlain(): string {
        return $this->plain;
    }

    final public function getDottedEventPath(): string {
        $path = $this->getEventPath();
        return implode('.', array_map('trim', $path));
    }

    public function setOrigin(): self {
        $this->origin = $this->getHeader('X-Forwarded-For') 
            ?? $this->getHeader('X-Real-IP') 
            ?? $_SERVER['REMOTE_ADDR'] 
            ?? null;
        return $this;
    }

    final public function setData(string $payload): self {
        $this->plain = $payload;
        $this->parse($payload);
        return $this;
    }

    
    // abstract methods
        /**
        * Parses the given payload string and returns the current instance of the PayloadAbstract class.
        *
        * This method is responsible for decoding the payload string and validating the resulting data structure.
        * If the payload is valid, it sets appropriate properties on the current instance of the PayloadAbstract class.
        *
        * @param string $payload The payload string to be parsed.
        * @return void
        */
        abstract protected function parse(string $payload): void;

        /**
         * Sets the event path components.
         *
         * @param array $event An array containing the event path to coincide with message routing.
         * @return $this The current instance of the PayloadAbstract class.
         */
        abstract public function setEvent(array $event): self;
}