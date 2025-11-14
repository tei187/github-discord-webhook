<?php

namespace tei187\GitDisWebhook\Handlers;

/**
 * Handles HTTP responses for the webhook.
 * 
 * @package tei187\GitDisWebhook\Handlers
 */
class ResponseHandler
{
    /**
     * Sends a JSON response with the specified message and HTTP status code.
     *
     * @param  string $message    The message to include in the JSON response.
     * @param  string $type       Type of response, lie "success" or "error".
     * @param  int    $statusCode The HTTP status code to use for the response.
     * @param  mixed  $data       (optional) Additional data to include in the response.
     * @return never
     */
    public static function send(string $message, string $type, int $statusCode, mixed $data = null): never
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        $response = [
            'responseCode' => $statusCode,
            'type' => $type, 
            'message' => $message
        ];
        if ($data !== null) {
            $response['data'] = $data;
        }
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
}
