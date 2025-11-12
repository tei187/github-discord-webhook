<?php

namespace tei187\GitDisWebhook\Helpers;

use tei187\GitDisWebhook\Helpers\UrlParser;
use tei187\GitDisWebhook\ValueObjects\Config;

/**
 * Provides methods to detect the type of webhook based on URL patterns and domains.
 * 
 * @package tei187\GitDisWebhook\Helpers
 */
class WebhookDetector
{
    /**
     * Strings comparison for domain.
     * 
     * @param string $url The URL to check.
     * @return bool True if a match is found, false otherwise.
     */
    public static function checkDomain(array $values, string $url): bool {
        $domain = UrlParser::extractDomain($url);
        foreach($values as $value) {
            if(stripos($domain, $value) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Strings comparison for URL, with/without protocol (switching if both have or at least one does not).
     * 
     * @param string $url The URL to check.
     * @return bool True if a match is found, false otherwise.
     */
    public static function checkUrl(array $values, string $url): bool {
        // protocol checks
        $urlHasProtocol = preg_match('#^https?://#i', $url);
        $urlNoProtocol = preg_replace('#^https?://#i', '', $url);

        // iteration
        foreach ($values as $value) {
            $valueHasProtocol = preg_match('#^https?://#i', $value);
            $valueNoProtocol = preg_replace('#^https?://#i', '', $value);

            if (!$urlHasProtocol) {
                // $url has no protocol: compare to $value without protocol
                if (strpos($url, $valueNoProtocol) === 0) {
                    return true;
                }
            } elseif (!$valueHasProtocol) {
                // $url has protocol, $value doesn't: compare $url without protocol to $value
                if (strpos($urlNoProtocol, $value) === 0) {
                    return true;
                }
            } else {
                // both have protocol: compare as-is
                if (strpos($url, $value) === 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Detects the webhook class based on the provided URL. Only the first matching webhook is returned.
     * 
     * It should be noted that if no checks are defined for a webhook, it cannot be auto detected, and can only be assigned manually in profile.
     *
     * @param string $url The URL to analyze.
     * @param Config|null $config The configuration object.
     * @return string|null The detected webhook class name, or null if none matched (or registry does not have corresponding entry).
     */
    public static function detect($url, ?Config $config = null): ?string
    {
        // Check if config is provided, if not, load 'webhooks' profiles from handler
        if($config !== null) {
            $webhooksConfig = &$config->webhooks ?? [];
        } else {
            $webhooksConfig = \tei187\GitDisWebhook\Handlers\ConfigHandler::load('webhooks');
        }

        foreach($webhooksConfig['detectors'] as $webhookName => $config) {
            $matches = [];

            // domain check
            if (isset($config['domain']) && is_array($config['domain'])) {
                foreach ($config['domain'] as $allowedDomain) {
                    $matches[] = (stripos(UrlParser::extractDomain($url), $allowedDomain) !== false) ? true : false;
                }
            }

            // URL pattern check
            if (isset($config['url']) && is_array($config['url'])) {
                foreach ($config['url'] as $urlPattern) {
                    $matches[] = (stripos($url, $urlPattern) !== false) ? true : false;
                }
            }

            // check if all matches are true.
            // if array is empty, then no checks were performed, in which case webhook cannot be auto detected.
            // further processing is skipped.
            if (!in_array(false, $matches, true) && !empty($matches)) {
                return $webhooksConfig['registry'][$webhookName] ?? null;
            }
        }
        
        return null;
    }
}