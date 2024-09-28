<?php

namespace tei187\GithubDiscordWebhook\Handlers;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;

class EventsHandler {
    /**
     * Handles an event received from the GitHub webhook.
     *
     * @param string $event The name of the event to handle.
     * @return string The go-to event.
     */
    static function handle(string $event) {
        $events = require __DIR__ . '/../../config/events.php';
        $eventsKeys = array_keys($events);
        $event = strtolower($event);

        if(in_array($event, $eventsKeys)) {
            return $events[$event];
            
        }

        ResponseHandler::send("No matching event handler found for event: $event", "error", 422);
    }
}