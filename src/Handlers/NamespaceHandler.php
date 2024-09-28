<?php

namespace tei187\GithubDiscordWebhook\Handlers;

class NamespaceHandler {
    private const BASE_NAMESPACE = 'tei187\\GithubDiscordWebhook\\';

    /**
     * Finds the class name for the given namespace segments.
     *
     * @param array $segments The namespace segments to convert to a class name.
     * @return string|null The fully qualified class name, or null if the class does not exist.
     */
    public static function findClass(array $segments): ?string
    {
        $namespace = self::BASE_NAMESPACE . implode('\\', array_map([self::class, 'toCamelCase'], $segments));

        if (class_exists($namespace)) {
            return $namespace;
        }

        return null;
    }

    /**
     * Converts a string to camel case.
     *
     * @param string $string The string to convert.
     * @return string The string in camel case.
     */
    private static function toCamelCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
