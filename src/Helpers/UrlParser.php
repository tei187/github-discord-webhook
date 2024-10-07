<?php

namespace tei187\GitDisWebhook\Helpers;

use tei187\GitDisWebhook\Enums\PathExtracts;

/**
 * Provides a set of utility methods for parsing and extracting information from URLs.
 * 
 * Generally speaking, it's a wrapper for `parse_url` function, with some specificity towards webhook use case in this package.
 */
class UrlParser
{
    /**
     * Extracts the webhook name from a given URL.
     *
     * @param string $url The URL to extract the webhook name from.
     * 
     * @return string|null|false The extracted webhook name.
     */
    public static function extractWebhookName(string $url)
    {
        $path = self::extractPath($url);

        if (!$path || is_null($path) || (is_string($path) && strlen(trim($path, '/')) === 0)) {
            return null;
        } else {
            $pathParts = explode('/', trim($path, '/'));
            return end($pathParts);
        }
    }

    /**
     * Extracts the domain from the given URL.
     *
     * @param string $url The URL to extract the domain from.
     * 
     * @return string|null|false The extracted domain.
     */
    public static function extractDomain(string $url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Extracts the protocol (e.g. "http", "https") from the given URL.
     *
     * @param string $url The URL to extract the protocol from.
     * 
     * @return string|null|false The extracted protocol.
     */
    public static function extractProtocol(string $url)
    {
        return parse_url($url, PHP_URL_SCHEME);
    }

    /**
     * Extracts the port from the given URL.
     *
     * @param string $url The URL to extract the port from.
     * 
     * @return string|null|false The extracted port.
     */
    public static function extractPort(string $url)
    {
        return parse_url($url, PHP_URL_PORT);
    }

    /**
     * Extracts the path from the given URL.
     *
     * @param string $url The URL to extract the path from.
     * 
     * @return string|null|false The extracted path.
     */
    public static function extractPath(string $url)
    {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * Extracts the query string from the given URL.
     *
     * @param string $url The URL to extract the query string from.
     * 
     * @return string|null|false The extracted query string.
     */
    public static function extractQuery(string $url)
    {
        return parse_url($url, PHP_URL_QUERY);
    }

    /**
     * Extracts the fragment (the part after the '#' symbol) from the given URL.
     *
     * @param string $url The URL to extract the fragment from.
     * 
     * @return string|null|false The extracted fragment.
     */
    public static function extractFragment(string $url)
    {
        return parse_url($url, PHP_URL_FRAGMENT);
    }

    /**
     * Extracts the username from the given URL.
     *
     * @param string $url The URL to extract the username from.
     * 
     * @return string|null|false The extracted username.
     */
    public static function extractUser(string $url)
    {
        return parse_url($url, PHP_URL_USER);
    }

    /**
     * Extracts the password from the given URL.
     *
     * @param string $url The URL to extract the password from.
     * 
     * @return string|null|false The extracted password.
     */
    public static function extractPass(string $url)
    {
        return parse_url($url, PHP_URL_PASS);
    }

    /**
     * Extracts the specified type of information from the given URL.
     *
     * @param string       $url  The URL to extract information from.
     * @param PathExtracts $type The type of information to extract from the URL.
     * 
     * @return string|array|null|false The extracted information, or array if $type is not set, or `null` if
     *                                 the type is not recognized, or boolean `false` if url is malformed.
     */
    public static function extract(string $url, PathExtracts $type = null) {
        return match ($type) {
            PathExtracts::WEBHOOK  => self::extractWebhookName($url),
            PathExtracts::DOMAIN   => self::extractDomain($url),
            PathExtracts::PROTOCOL => self::extractProtocol($url),
            PathExtracts::PORT     => self::extractPort($url),
            PathExtracts::PATH     => self::extractPath($url),
            PathExtracts::QUERY    => self::extractQuery($url),
            PathExtracts::FRAGMENT => self::extractFragment($url),
            PathExtracts::USER     => self::extractUser($url),
            PathExtracts::PASS     => self::extractPass($url),
            default                => parse_url($url)
        };
    }
}
