<?php

namespace tei187\GitDisWebhook\Handlers\GitHub;

use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;

/**
 * Resolves and processes the payload received from Git-based webhooks.
 * 
 * This class handles the interpretation and extraction of relevant data
 * from the webhook payload, facilitating further actions based on the
 * received information.
 * 
 * @package tei187\GitDisWebhook\Handlers\GitHub
 */
class PayloadResolver 
{
    /**
     * Resolves whether a push event is about tags, branches or general commit
     *
     * @param  PayloadInterface $payload The payload from the GitHub webhook
     * @return string The type of push event ('tag', 'branch', or 'commit')
     */
    private static function pushEventType($payload): string {
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
     * @param  string       $payload        Plain text JSON payload from GitHub call.
     * @param  array        $payloadsConfig Array with configured payload classes.
     * @return ?string|void `null` if not found, class name if found, or `ResponseHandler` void due to lack of event name in header.
     * 
     * @todo rewrite to something more self-managable
     */
    public static function find(string $payload, array $payloadsConfig): ?string {
        if(!key_exists('HTTP_X_GITHUB_EVENT', $_SERVER)) {
            throw new \InvalidArgumentException("No X-GitHub-Event header found in the request.");
        }

        $payload = json_decode($payload);

        return match($_SERVER['HTTP_X_GITHUB_EVENT']) {
            'push' => match(self::pushEventType($payload)) {
                'tags'   => $payloadsConfig['push']['tag'] ?? null,
                'branch' => $payloadsConfig['push']['branch'] ?? null,
                'commit' => $payloadsConfig['push']['commit'] ?? null,
            },
            'fork'    => $payloadsConfig['fork'] ?? null,
            'release' => $payloadsConfig['release'] ?? null,
            'ping'    => $payloadsConfig['ping'] ?? null,
            default => null,
        };
    }
}
