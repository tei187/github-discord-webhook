<?php

namespace tei187\GitDisWebhook\Messages;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Interfaces\MessageInterface;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;

/**
 * Defines the base abstract class for all message types that can be sent to a webhook.
 * This class provides common functionality and properties for managing the message content
 * and the webhook instance that the message will be sent to. Implements `MessageInterface`.
 */
abstract class MessageAbstract implements MessageInterface {
    /**
     * The message content to be sent to the webhook.
     * 
     * @var null|string
     */
    protected ?string $message;

    /**
     * Indicates whether the message has been successfully sent to the webhook.
     *
     * @var bool
     */
    protected bool $sent = false;
    
    /**
     * The webhook instance that the message will be sent to.
     * Shared object reference with wrapping service class.
     *
     * @var WebhookInterface
     */
    protected WebhookInterface $webhook;

    /**
     * Constructs a new instance of the message class, associating it with the provided webhook.
     *
     * @param WebhookInterface|null $webhook The webhook instance that the message will be sent to. If not provided or error,
     *                                       the message will not be able to be sent.
     */
    function __construct(?WebhookInterface $webhook = null) {
        if($webhook !== null) {
            $this->webhook = $webhook;
        }
        $this->create();
    }

    /**
     * Creates a new message content for the given webhook and event path, assigning the result to the `message` property.
     *
     * @abstract
     * @return void
     */
    abstract protected function create();

    /**
     * Sends the message to the configured webhook.
     *
     * @return bool The response from the webhook, or false on failure.
     */
    protected function webhookCall(): bool {
        $data = array('content' => $this->message);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($this->webhook->url, false, $context);

        return 
            $result !== false 
                ? true 
                : false;
    }

    public function setWebhook(?WebhookInterface $webhook): self {
        $this->webhook = $webhook;
        return $this;
    }

    /**
     * Sends the message to the configured webhook and handles the response.
     *
     * This method calls the `webhookCall()` method to send the message to the configured webhook. If the webhook call is
     * successful, it calls the `successResponse()` method. If the webhook call fails, it calls the `failedResponse()` method.
     *
     * @return void
     */
    public function send(): void {
        if($this->webhook === null) {
            throw new \Exception("Webhook is not set for the message.");
        }

        $this->sent = $this->webhookCall();

        $this->sent === true
            ? $this->successResponse()
            : $this->failedResponse();
    }

    // responses
        public function successResponse(): ResponseHandler {
            return ResponseHandler::send("Message sent successfully.", "success", 200);
        }

        public function failedResponse(): ResponseHandler {
            return ResponseHandler::send("Payload received but message failed to send to channel.", "error", 400);
        }

    // magic getter
        public function __get($param) {
            if(isset($this->$param)) {
                return match ($param) {
                    'message' => $this->message,
                    'sent'    => $this->status,
                    'webhook' => $this->webhook,
                    default   => null
                };
            }
        }
}