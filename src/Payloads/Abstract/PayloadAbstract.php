<?php

namespace tei187\GithubDiscordWebhook\Payloads\Abstract;

use tei187\GithubDiscordWebhook\Handlers\ArrayHandler;
use tei187\GithubDiscordWebhook\Interfaces\Payload as PayloadInterface;

abstract class PayloadAbstract implements PayloadInterface {
    /**
     * @var string The raw JSON payload of the GitHub webhook request.
     */
    protected string $plain;
    
    /**
     * @var string The type of GitHub event that triggered the webhook.
     */
    protected string $event;
    
    /**
     * @var ?string *(optional)* The subject of the GitHub webhook payload, mainly for `push` events (`commit` or `tag` or `branch`).
     *              Whether it is used or not will depend on the abstraction classes and applicability.
     */
    protected ?string $subject;

    /**
     * @var ?string *(optional)* The type of action that triggered the GitHub webhook. It is considered the action applied to the subject or event itself (e.g. `created`).
     *              Whether it is used or not will depend on the abstraction classes and applicability.
     */
    protected ?string $action;
    
    /**
     * @var \stdClass The name of the GitHub repository that triggered the webhook. 
     *                If the payload is parsed, it will have the following properties: `name`, `fullname`, `url`.
     */
    protected object $repo;

    /**
     * @var bool Indicates whether the payload is allowed to be processed in messaging.
     */
    protected bool $allowed;

    /**
     * Constructs a new payload object with an optional JSON payload.
     *
     * @param string|null $payload The JSON payload to be parsed, or `null` if no payload is provided.
     */
    public function __construct( ?string $payload = null ) {
        $payload ? $this->parse($payload) : null;
    }

    /**
     * Checks if the payload is allowed to be processed based on the configuration.
     *
     * This method filters the payload properties (event, subject, action) to create a dot-notation path, and then checks the corresponding value in the payloads.php configuration file. If the value is a boolean, it is used to set the $allowed property. Otherwise, $allowed is set to false.
     *
     * @return $this The current instance of the PayloadAbstract class.
     */
    public function checkAllowed(): self {
        $config = require(GHDWEBHOOK_ROOT . 'config/allowed.php');

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

    /**
     * Gets a dotted path representation of the event, subject, and action properties.
     *
     * This method constructs a dotted path string by filtering out any null or empty values from the event, subject, and action properties, and then joining the remaining values with periods.
     *
     * @return string The dotted path representation of the event, subject, and action properties.
     */
    public function getDottedPath(): string {
        $path = array_filter(
            [ $this->event, $this->subject, $this->action, ],
            function($value) { return $value !== null && $value !== ''; }
        );
        return implode('.', array_map('trim', $path));
    }

    // static classes
    static public function makeRepo(object $decoded): \stdClass {
        $repo = new \stdClass();
        $repo->name     = (string) $decoded->repository->name;
        $repo->fullname = (string) strtolower($decoded->repository->full_name);
        $repo->url      = (string) $decoded->repository->html_url;
        return $repo;
    }
    
    // abstract classes
    
    /**
     * Parses the given payload string and returns the current instance of the PayloadAbstract class.
     *
     * This method is responsible for decoding the payload string and validating the resulting data structure.
     * If the payload is valid, it sets appropriate properties on the current instance of the PayloadAbstract class.
     *
     * @param string $payload The payload string to be parsed.
     * @return $this The current instance of the PayloadAbstract class.
     */
    abstract public function parse(string $payload): self;

    // magic getter

    public function __get($param) {
        if(isset($this->$param)) {
            return $this->$param;
        }
    }
}