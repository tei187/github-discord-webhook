<?php

namespace tei187\GitDisWebhook\Services\Abstract;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\ServiceInterface;
use tei187\GitDisWebhook\Interfaces\MessageInterface;
use tei187\GitDisWebhook\Interfaces\PayloadFactoryInterface;
use tei187\GitDisWebhook\Interfaces\MessageFactoryInterface;
use tei187\GitDisWebhook\Factories\MessageFactory;
use tei187\GitDisWebhook\Handlers\ArrayHandler;
use tei187\GitDisWebhook\Traits\UsesMagicGetter;

/**
 * Abstract base class for services handling webhook requests.
 * 
 * The ServiceAbstract class provides a common structure and functionality for
 * specific service implementations. It manages configuration, payload, webhook,
 * and payload factory instances, and enforces the implementation of payload validation.
 *
 * @uses \tei187\GitDisWebhook\Traits\UsesMagicGetter
 *
 * @package tei187\GitDisWebhook\Services\Abstract
 */
abstract class ServiceAbstract implements ServiceInterface {
    use UsesMagicGetter;

    // general platform/service config processing
        /**
         * Instance of app configuration.
         * @var Config
         */
        protected Config $config;

        /**
        * Service name identifier. Used for service detection and routing.
        * @var string
        */
        protected string $name;

    // webhook processing
        /**
         * Instance of webhook handler.
         * @var WebhookInterface|null
         */
        protected ?WebhookInterface $webhook = null;

    // payload processing
        /**
         * Class name of the service-specific payload factory.
         * 
         * Most likely, it will be tied with the service, hence by default it is set to null and should be defined in child classes.
         * 
         * @var string|null
         */
        protected ?string $payloadFactoryClass;

        /**
         * Instance of the service-specific payload factory.
         * @var PayloadFactoryInterface|null
         */
        protected ?PayloadFactoryInterface $payloadFactory;


        /**
         * Class name of the service-specific payload.
         * 
         * Most likely, it will be tied with the service, hence by default it is set to null and should be defined in child classes.
         * For more complex services, it may be better to use the payload factory instead of direct class instantiation, unless you have
         * a specific one-fit-all payload class.
         * 
         * @var string|null
         */
        protected ?string $payloadClass = null;

        /**
         * Instance of the payload.
         * @var PayloadInterface|null
         */
        protected ?PayloadInterface $payload;

    // messages processing
        /**
         * Class name of the service-specific message factory.
         * 
         * Most likely it will be the general MessageFactory, but in case of special requirements, a service-specific one can be used,
         * hence by default it is set to `\tei187\GitDisWebhook\Factories\MessageFactory`.
         * 
         * @var string|null
         */
        protected ?string $messageFactoryClass = \tei187\GitDisWebhook\Factories\MessageFactory::class;
    
        /**
         * Instance of the message factory.
         * @var MessageFactory|null
        */
        protected ?MessageFactoryInterface $messageFactory;

        /**
         * Class name of the service-specific payload.
         * 
         * Most likely, it will be tied with the service, hence by default it is set to null and should be defined in child classes.
         * For more complex services, it may be better to use the message factory instead of direct class instantiation, unless you have
         * a specific one-fit-all message class.
         * 
         * @var string|null
         */
        protected ?string $messageClass = null;
        
        /**
         * Instance of the message handler.
         * @var MessageInterface|null
         */
        protected ?MessageInterface $message;

    /**
     * Constructs a new ServiceAbstract instance.
     *
     * @param Config|null $config The configuration for the service.
     * @param PayloadInterface|null $payload The payload for the service.
     * @param WebhookInterface|null $webhook The webhook for the service.
     * @param MessageInterface|null $message The message factory for the service.
     */
    public function __construct(?Config $config = null, ?PayloadInterface $payload = null, ?WebhookInterface $webhook = null, ?MessageInterface $message = null) {
        // set config through injection or create new instance if not provided
        $config = $config ?? new Config();
        $this->setConfig($config);

        // set webhook, if provided
        if($webhook !== null) { $this->setWebhook($webhook); }

        // set payload ...
        if($payload !== null) {
            // ... directly, if provided
            $this->setPayload($payload);
        } elseif ($payload === null && $this->payloadFactoryClass !== null) {
            // ... through factory, if class is defined
            $this->setPayloadFactory()->setPayloadThroughFactory(file_get_contents('php://input'));
        }

        // set message ...
        if($message !== null) {
            // ... directly, if provided
            $this->setMessage($message);
        } elseif ($message === null && $this->messageFactoryClass !== null && $this->webhook !== null) {
            // ... through factory, if class is defined
            $this->setMessageFactory()->setMessageThroughFactory($this->payload?->event ?? '');
        }
    }

    final public function setWebhook(?WebhookInterface $webhook): self {
        $this->webhook = $webhook; 
        return $this;
    }
    
    final public function setConfig(Config $config): self {
        $this->config = $config;
        return $this;
    }

    final public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    // PAYLOAD MANAGEMENT

        /**
         * @throws \InvalidArgumentException If the provided payload is invalid.
         */
        public function setPayload(PayloadInterface|string|null $payload, ?string $payloadClass = null): self {
            $check = $this->validatePayload($payload);

            if($payload instanceof PayloadInterface) {
                // assign provided payload instance
                $this->payload = $payload;
            } elseif (is_string($payload) && $payloadClass !== null && class_exists($payloadClass)) {
                // instantiate specified payload class
                $this->payload = new $payloadClass($payload);
            } elseif (is_string($payload) && $this->payloadClass !== null && class_exists($this->payloadClass)) {
                // instantiate default payload class
                $this->payload = new $this->payloadClass($payload);
            } else {
                // reset payload to null
                $this->payload = null;
            }

            $this->payload = $check 
                ? $this->payload 
                : throw new \InvalidArgumentException("Invalid payload provided.");

            return $this;
        }

        /**
         * @throws \InvalidArgumentException If no payload factory class is provided.
         * @throws \InvalidArgumentException If the provided payload factory class does not exist.
         */
        final public function setPayloadFactory(?string $payloadFactoryClass = null): self {
            // use provided class or default one
            $payloadFactoryClass = $payloadFactoryClass ?? $this->payloadFactoryClass;
            // throw: no class provided
            if($payloadFactoryClass === null) { throw new \InvalidArgumentException("No payload factory class provided."); }            
            // throw: class does not exist
            if(!class_exists($payloadFactoryClass)) { throw new \InvalidArgumentException("Payload factory class does not exist: {$payloadFactoryClass}"); }
            // instantiate factory
            $this->payloadFactory = new $payloadFactoryClass($this->config);

            return $this;
        }

        /**
         * @throws \InvalidArgumentException If the payload factory is not set.
         */
        final public function setPayloadThroughFactory(string $payload): self {
            // throw: payload factory is not set
            if(!isset($this->payloadFactory)) { throw new \InvalidArgumentException("Payload factory is not set."); }
            // create payload through factory
            $this->payload = $this->payloadFactory->createPayload($payload);

            return $this;
        }

    // MESSAGE MANAGEMENT

        final public function setMessage(MessageInterface|null $message = null, ?string $messageClass = null): self {
            if($message instanceof MessageInterface) {
                // assign provided message instance
                $this->message = $message;
            } elseif ($message === null && $messageClass !== null && class_exists($messageClass)) {
                // instantiate specified message class
                $this->message = new $messageClass();
            } elseif ($message === null && $this->messageFactoryClass !== null && class_exists($this->messageFactoryClass)) {
                // instantiate message through message factory class
                $this->message = new $this->messageFactoryClass();
            } else {
                // reset message to null
                $this->message = null;
            }
            return $this;
        }

        final public function setMessageFactory(?MessageFactoryInterface $messageFactory = null): self {
            // assign provided factory ($messageFactory) or
            // instantiate specified factory with current config, service name and webhook
            $this->messageFactory = $messageFactory ?? new MessageFactory($this->config, $this->name, $this->webhook);
            return $this;
        }

        /**
         * @throws \InvalidArgumentException If the message factory or webhook is not set.
         */
        final public function setMessageThroughFactory(string $event): self {
            // throw: message factory is not set
            if(!isset($this->messageFactory)) { throw new \InvalidArgumentException("Message factory is not set."); }
            // throw: webhook is not set
            if(!isset($this->webhook)) { throw new \InvalidArgumentException("Webhook is not set."); }
            // create message through factory
            $this->message = $this->messageFactory->createMessage($event, $this->payload, $this->webhook);

            return $this;
        }

    abstract public function validatePayload(?string $payload = null): bool;
    abstract public function getRepositoryName(): ?string;
    abstract public function getRepositoryBranch(): ?string;

    public function validateEvent(): bool {
        // merge allowed events from config and webhook profile overrides
        $compiledEvents = ArrayHandler::array_merge_deep_overwrite(
            $this->config->allowed[$this->name] ?? [],
            $this->config->profiles[$this->webhook->name]['overrides']['allowed'] ?? []
        );
        // check if event path exists in compiled allowed events
        return ArrayHandler::getValueByDotNotation( $compiledEvents, $this->payload->getDottedEventPath() ) ?? true;
    }
}