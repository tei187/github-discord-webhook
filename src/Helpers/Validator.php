<?php

namespace tei187\GitDisWebhook\Helpers;

/**
 * Provides validation methods for various data types and structures.
 * 
 * @package tei187\GitDisWebhook\Helpers
 */
class Validator {
    /**
     * Check if a string is a valid JSON string.
     * 
     * @param string $string
     * @return bool
     */
    public static function isJsonString(string $string): bool {
        json_decode($string, true);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Check if the provided payload is valid.
     * 
     * @param string|object $payload String-based or object-based payload.
     * @param bool $allowJustString Whether to allow just a string as valid payload, without JSON validation, for non-JSON payloads.
     * @return bool
     */
    public static function isPayload(string|object $payload, $allowJustString = false): bool {
        if(is_string($payload)) {
            return $allowJustString 
                ? true 
                : self::isJsonString($payload);
        }

        return $payload instanceof \tei187\GitDisWebhook\Interfaces\PayloadInterface;
    }
}