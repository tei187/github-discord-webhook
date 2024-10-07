<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\Handlers\ResponseHandler;

/**
 * Defines the interface for a message that can be sent, with success and failure responses.
 */
interface Message {
    /**
     * Sends the message.
     */
    public function send(): void;

    /**
     * Handles the success response for the message.
     */
    public function successResponse(): ResponseHandler;

    /**
     * Handles the failed response for the message.
     */
    public function failedResponse(): ResponseHandler;
}