<?php

namespace tei187\GitDisWebhook\Handlers;

/**
 * Provides utility methods for working with arrays and objects.
 * 
 * @package tei187\GitDisWebhook\Handlers
 */
class ArrayHandler {
    /**
     * Gets the value from an array or object using dot notation.
     *
     * @param  array|object $array The array or object to retrieve the value from.
     * @param  string       $path  The dot-separated path to the value.
     * @return mixed|null The value at the specified path, or null if the path does not exist.
     */
    public static function getValueByDotNotation($array, $path) {
        $keys = explode('.', $path);
        $value = $array;
    
        foreach ($keys as $key) {
            if (is_array($value) && isset($value[$key])) {
                $value = $value[$key];
            } elseif (is_object($value) && isset($value->$key)) {
                $value = $value->$key;
            } else {
                return null;
            }
        }
    
        return $value;
    }

    /**
     * Filters an array, removing any elements that are null, empty, or contain only whitespace.
     * 
     * Yes, the method's name is misleading. What are you gonna do about it?
     * NOTHING!
     * Exactly. Just as I thought.
     *
     * @param array $array The input array to filter.
     * @return array The filtered array.
     */
    public static function filterNulls($array): array {
        return array_filter(
            $array,
            function($value) { return $value !== null && trim($value) !== '' && strlen(trim($value)) >= 1; }
        );
    }

    /**
     * Recursively converts an array to an object.
     *
     * @param mixed $array The array to convert.
     * @return mixed The converted object, or the original value if not an array.
     */
    public static function arrayToObject($array) {
        if (!is_array($array)) {
            return $array;
        }
        $object = new \stdClass();
        foreach ($array as $key => $value) {
            $object->{$key} = self::arrayToObject($value);
        }
        return $object;
    }

    /**
     * Merges two arrays recursively, with values from the second array overwriting those in the first.
     *
     * @param array $a The first array.
     * @param array $b The second array.
     * @return array The merged array.
     */
    public static function array_merge_deep_overwrite(array $a, array $b): array {
        foreach ($b as $key => $value) {
            if (is_array($value) && isset($a[$key]) && is_array($a[$key])) {
                $a[$key] = self::array_merge_deep_overwrite($a[$key], $value);
            } else {
                $a[$key] = $value;
            }
        }
        return $a;
    }
}