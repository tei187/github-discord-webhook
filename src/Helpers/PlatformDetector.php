<?php

namespace tei187\GitDisWebhook\Helpers;

/**
 * A helper class for detecting platform-specific request characteristics.
 *
 * The PlatformDetector class provides static methods to verify if incoming requests
 * meet certain criteria, such as required headers, expected values, and allowed origins.
 * 
 * @package tei187\GitDisWebhook\Helpers
 */
class PlatformDetector {
    /**
     * Checks if all required headers are present in the server headers.
     * @param array $requiredHeaders An associative array where keys are header names and values are booleans indicating if the header is required.
     * @param array $serverHeaders An associative array of server headers.
     * @return bool True if all required headers are present, false otherwise.
     */
    public static function checkRequiredHeaders(array $requiredHeaders, array $serverHeaders): bool {
        foreach ($requiredHeaders as $header => $required) {
            if ($required && !isset($serverHeaders[$header])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the server headers match the expected header values.
     * @param array $headerValues An associative array where keys are header names and values are expected substrings.
     * @param array $serverHeaders An associative array of server headers.
     * @return bool True if all header values match, false otherwise.
     */
    public static function checkHeaderValuesMatch(array $headerValues, array $serverHeaders): bool {
        foreach ($headerValues as $header => $expectedValue) {
            if (isset($serverHeaders[$header])) {
                if (strcasecmp($serverHeaders[$header], $expectedValue) !== 0) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the server headers contain the expected substrings.
     * @param array $headerValues An associative array where keys are header names and values are expected substrings.
     * @param array $serverHeaders An associative array of server headers.
     * @return bool True if all header values contain the expected substrings, false otherwise.
     */
    public static function checkHeaderValuesContain(array $headerValues, array $serverHeaders): bool {
        foreach ($headerValues as $header => $expectedSubstring) {
            if (isset($serverHeaders[$header])) {
                if (stripos($serverHeaders[$header], $expectedSubstring) === false) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the request method matches the expected method.
     * @param string|null $expectedMethod The expected request method (e.g., 'POST'). If null, any method is accepted.
     * @param string|null $actualMethod The actual request method from the server. If null, treated as no method.
     * @return bool True if the methods match or if expectedMethod is null, false otherwise.
     */
    public static function checkRequestMethod(?string $expectedMethod, ?string $actualMethod): bool {
        if ($expectedMethod === null) {
            return true;
        }
        return strcasecmp($expectedMethod, $actualMethod) === 0;
    }

    /**
     * Checks if the request origin is in the list of allowed origins.
     * @param array $allowedOrigins An array of allowed origin substrings.
     * @param string|null $origin The actual origin from the request. If null, treated as no origin.
     * @return bool True if the origin is allowed or if allowedOrigins is empty, false otherwise.
     */
    public static function checkOrigin(array $allowedOrigins, ?string $origin): bool {
        if (empty($allowedOrigins)) {
            return true;
        }
        if ($origin === null) {
            return false;
        }
        foreach ($allowedOrigins as $allowedOrigin) {
            if (stripos($origin, $allowedOrigin) !== false) {
                return true;
            }
        }
        return false;
    }
}