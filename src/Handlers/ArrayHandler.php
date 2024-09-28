<?php

namespace tei187\GithubDiscordWebhook\Handlers;

class ArrayHandler {
    /**
     * Gets the value from an array or object using dot notation.
     *
     * @param array|object $array The array or object to retrieve the value from.
     * @param string $path The dot-separated path to the value.
     * @return mixed|null The value at the specified path, or null if the path does not exist.
     */
    static public function getValueByDotNotation($array, $path) {
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
}