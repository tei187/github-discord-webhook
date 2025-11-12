<?php

namespace tei187\GitDisWebhook\ValueObjects;

// factories
use tei187\GitDisWebhook\Factories\ServiceFactory;
use tei187\GitDisWebhook\Factories\WebhookFactory;
// interfaces
use tei187\GitDisWebhook\Interfaces\ServiceInterface;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
// other
use tei187\GitDisWebhook\Helpers\UrlParser;
use tei187\GitDisWebhook\Traits\UsesMagicGetter;
use tei187\GitDisWebhook\Handlers\ResponseHandler;

/**
 * Represents a worker that processes incoming webhook requests.
 * 
 * The Worker class is responsible for extracting the webhook name from the request URI,
 * loading the corresponding profile configuration, and creating the appropriate service
 * and webhook instances to handle the incoming payload.
 * 
 * Origin of the shared configuration object throughout application cycle.
 * 
 * @uses tei187\GitDisWebhook\Traits\UsesMagicGetter
 * 
 * @package tei187\GitDisWebhook\ValueObjects
 */
class Worker {
    use UsesMagicGetter;

    protected Config $config;
    protected ServiceInterface $service;
    protected object $profile;
    protected string $name;

    /**
     * Constructs a new Worker instance.
     *
     * Extracts the webhook name from the request URI and loads the corresponding profile configuration.
     *
     * @throws \InvalidArgumentException If no profile is found for the extracted webhook name.
     */
    public function __construct(?Config $config = null) {
        $this->config = $config ?? new Config();
        $this->name = UrlParser::extractWebhookName($_SERVER['REQUEST_URI']) ?? throw new \InvalidArgumentException("No webhook name found in the request URI.");

        // extract profile
        if (array_key_exists($this->name, $this->config->profiles)) {
            $this->profile = (object) array_merge((array) $this->config->profiles_defaults, (array) $this->config->profiles[$this->name]);
        } else {
            throw new \InvalidArgumentException("No profile found for webhook name: {$this->name}");
        }
    }

    /**
     * Creates the service instance based on the profile configuration.
     *
     * @return ServiceInterface The created service instance.
     */
    protected function makeService(): ServiceInterface {
        // determine requirement for service lookup
        $service = match (true) {
             // auto-detect supported services
            is_array($this->profile->services) && empty($this->profile->services) => "auto",

            // specific service designation (single element string array)
            is_array($this->profile->services) && count($this->profile->services) === 1
            && class_exists($this->profile->services[0]) => $this->profile->services[0],

            // auto-detect within constraints
            is_array($this->profile->services) && count($this->profile->services) > 1 => "constrained",

            // specific service designation (string)
            is_string($this->profile->services) && class_exists($this->profile->services)
            && $this->profile->services instanceof ServiceInterface => $this->profile->services,

            // auto-detect supported services for any parameter not matching other expressions
            default => "auto",
        };

        // set limit for constrained detection
        $limit = $service === "constrained" ? $this->profile->services : [];

        // leftover overwrite to null for auto/constrained
        if(in_array($service, ["auto", "constrained"])) {
            $service = null;
        }

        return (new ServiceFactory($this->config))->createService($service, $limit);
    }

    /**
     * Creates the webhook instance based on the profile configuration.
     *
     * @return WebhookInterface The created webhook instance.
     */
    protected function makeWebhook(): WebhookInterface {
        $webhookClass = $this->profile->webhook['class'] ?? null;
        return (new WebhookFactory($this->config))->createWebhook($this->name, $webhookClass);
    }

    /**
     * Processes the incoming call by initializing the service and webhook, and setting the payload.
     *
     * @param string|null $payload The raw payload data. If null, it will be read from `php://input`.
     * @return void
     */
    public function processCall(?string $payload = null): void {
        // initialize service and webhook
        $this->service = $this->makeService();
        $this->service->setWebhook($this->makeWebhook());
        
        // set payload through factory
        if($this->service->payloadFactory !== null) {
            $this->service->setPayloadThroughFactory($payload ?? file_get_contents('php://input'));
        } else {
            $this->service->setPayload($payload ?? file_get_contents('php://input'));
        }

        // assign payload to webhook
        $this->service->webhook->setPayload($this->service->payload);

        // validate if payload is allowed for this webhook (event validation)
        if($this->service->validateEvent()) {
            // message through factory
            $this->service->setMessageFactory();
            $this->service->setMessageThroughFactory(
                $this->service->event,
                $this->service->payload,
                $this->service->webhook
            );
    
            // send message
            $this->service->message->send();
        } else {
            ResponseHandler::send("Payload received but not allowed to send to channel due to webhook's config.", "success", 200);
        }

    }
}