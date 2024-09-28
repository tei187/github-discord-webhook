<?php
namespace tei187\GithubDiscordWebhook\Messages;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;
use tei187\GithubDiscordWebhook\Interfaces\Message as MessageInterface;
use tei187\GithubDiscordWebhook\Interfaces\Webhook as WebhookInterface;

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
     *
     * @var WebhookInterface
     */
    protected WebhookInterface $webhook;

    /**
     * Constructs a new instance of the message class, associating it with the provided webhook.
     *
     * @param WebhookInterface $webhook The webhook instance that the message will be sent to. If not provided or error, the message will not be able to be sent.
     */
    function __construct(WebhookInterface $webhook) {
        $this->webhook = $webhook;
        $this->create();
    }

    /**
     * Creates a new message instance for the given webhook.
     *
     * @abstract
     * @return self
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
        $context  = stream_context_create($options);
        $result = file_get_contents($this->webhook->url, false, $context);

        return 
            $result !== false 
                ? true 
                : false;
    }

    /**
     * Sends the message to the configured webhook and handles the response.
     *
     * This method calls the `webhookCall()` method to send the message to the configured webhook. If the webhook call is successful, it calls the `successResponse()` method. If the webhook call fails, it calls the `failedResponse()` method.
     *
     * @return void
     */
    public function send(): void {
        if($this->webhook->payload->allowed !== true) {
            ResponseHandler::send("Payload received but not allowed to send to channel due to webhook's config.", "success", 200);
        }

        $this->sent = $this->webhookCall();

        $this->sent === true
            ? $this->successResponse()
            : $this->failedResponse();
    }

    /**
     * Sends a success response to the ResponseHandler.
     *
     * This method is called when the message is successfully sent to the configured webhook's channel.
     */
    public function successResponse() {
        ResponseHandler::send("Message sent successfully.", "success", 200);
    }

    /**
     * Sends a failure response to the ResponseHandler.
     *
     * This method is called when the message fails to be sent to the configured webhook's channel.
     */
    public function failedResponse() {
        ResponseHandler::send("Payload received but message failed to send to channel.", "error", 400);
    }

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