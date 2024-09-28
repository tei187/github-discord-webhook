<?php

namespace tei187\GithubDiscordWebhook\Handlers;

/**
 * Handles HTTP responses for the webhook.
 */
class ResponseHandler
{
    /**
     * Sends a JSON response with the specified message and HTTP status code.
     *
     * @param string $message The message to include in the JSON response.
     * @param string $type Type of response, lie "success" or "error".
     * @param int $statusCode The HTTP status code to use for the response.
     */
    public static function send(string $message, string $type, int $statusCode): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode([
            'responseCode' => $statusCode,
            'type' => $type, 
            'message' => $message
        ], JSON_PRETTY_PRINT);
        exit;
    }
}
