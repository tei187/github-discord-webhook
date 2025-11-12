<?php

namespace tei187\GitDisWebhook\Payloads\Abstract;

use tei187\GitDisWebhook\Payloads\Abstract\PayloadAbstract;
use tei187\GitDisWebhook\Handlers\ArrayHandler;

/**
 * Defines the base abstract class for handling GitHub webhook payloads.
 * This class provides common properties and methods for working with GitHub webhook payloads.
 * Implements `PayloadInterface` through `PayloadAbstract`.
 * 
 * @abstract
 * @uses \tei187\GitDisWebhook\Traits\UsesMagicGetter
 * 
 * @package tei187\GitDisWebhook\Payloads\Abstract
 */
abstract class GitHubPayloadAbstract extends PayloadAbstract {
    /**
     * @var string The type of GitHub event that triggered the webhook. Required minimal parameter for event path chains.
     */
    protected string $event;
    
    /**
     * @var ?string *(optional)* The subject of the GitHub webhook payload, mainly for `push` events (`commit` or `tag` or `branch`).
     *              Whether it is used or not will depend on the abstraction classes and applicability.
     */
    protected ?string $subject;

    /**
     * @var ?string *(optional)* The type of action that triggered the GitHub webhook. It is considered the action applied to the
     *              subject or event itself (e.g. `created`). Whether it is used or not will depend on the abstraction classes and
     *              applicability.
     */
    protected ?string $action;

    /**
     * Constructs a new payload object with an optional JSON payload.
     *
     * @param string|null $payload The JSON payload to be parsed, or `null` if no payload is provided.
     */
    public function __construct( ?string $payload = null ) {
        $payload ? $this->parse($payload) : null;
    }

    /**
     * Gets an array representation of the event, subject, and action properties.
     * This method returns an array representation of the event, subject, and action properties, filtering out any null or empty values.
     * @return array An array representation of existing (not null) event, subject, and action properties.
     * @see getDottedEventPath()
     */
    public final function getEventPath(): array {
        return ArrayHandler::filterNulls( [ $this->event, $this->subject, $this->action ] );
    }

    public function setEvent(array $event): self {
        $this->event = $event[0] ?? '';
        $this->subject = $event[1] ?? null;
        $this->action = $event[2] ?? null;
        return $this;
    }
        
    // abstract methods
        abstract protected function parse(string $payload): void;
}