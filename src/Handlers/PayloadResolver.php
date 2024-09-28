<?php

namespace tei187\GithubDiscordWebhook\Handlers;

use tei187\GithubDiscordWebhook\Handlers\ResponseHandler;

class PayloadResolver {
    /**
     * Resolves whether a push event is about tags, branches or general commit
     *
     * @param \stdClass $payload The payload from the GitHub webhook
     * @return string The type of push event ('tag', 'branch', or 'commit')
     */
    private static function pushEventType(\stdClass $payload)
    {
        if (isset($payload->ref)) {
            if (strpos($payload->ref, 'refs/tags/') === 0) {
                return 'tags';
            } elseif (strpos($payload->ref, 'refs/heads/') === 0) {
                if($payload->created || $payload->deleted) {
                    return 'branch';
                }
            }
        }
        return 'commit';
    }

    /**
     * Finds which payload should be loaded, basing on the header and configuration array.
     *
     * @param string $payload Plain text JSON payload from GitHub call.
     * @param array  $payloadsConfig Array with configured payload classes.
     * @return ?string|void `null` if not found, class name if found, or ResponseHandler void due to lack of event name in header.
     */
    public static function find(string $payload, array $payloadsConfig): ?string {
        $_SERVER['HTTP_X_GITHUB_EVENT'] ?: ResponseHandler::send("No event provided in HTTP_X_GITHUB_EVENT header.", "error", 400);

        $payload = json_decode($payload);

        return match($_SERVER['HTTP_X_GITHUB_EVENT']) {
            'push' => match(self::pushEventType($payload)) {
                'tags'   => $payloadsConfig['push']['tag'] ?? null,
                'branch' => $payloadsConfig['push']['branch'] ?? null,
                'commit' => $payloadsConfig['push']['commit'] ?? null,
            },
            'fork'    => $payloadsConfig['fork'] ?? null,
            'release' => $payloadsConfig['release'] ?? null,
            'ping' => $payloadsConfig['ping'] ?? null,
            default => null,
        };
    }
}
