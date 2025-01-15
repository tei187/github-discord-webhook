<?php

namespace tei187\GitDisWebhook\Helpers;

class Markdown {
    /**
     * Converts a string with newline characters into an array of lines.
     *
     * @param string $string The input string to be converted.
     * @return string[] An array of lines, where each element represents a line from the input string.
     */
    public static function newlineToArray($string) {
        return explode("\n", $string);
    }

    /**
     * Converts an array of strings into a single string, with each element separated by a newline character.
     *
     * @param string[] $array The input array of strings to be converted.
     * @return string The resulting string with each array element on a new line.
     */
    public static function arrayToLine($array) {
        return implode("\n", $array);
    }

    /**
     * Converts an array of strings into a single string, with each element prefixed by the given string.
     *
     * @param string[] $array The input array of strings to be converted.
     * @param string $prefix The prefix to be added to each line.
     * @return string The resulting string with each array element prefixed and separated by a newline character.
     */
    public static function arrayToLineWithPrefix($array, $prefix) {
        return implode("\n", array_map(function($item) use ($prefix) {
            return $prefix . $item;
        }, $array));
    }

    /**
     * Converts an array of strings into a single string, with each element prefixed by the given string and suffixed by the given string.
     *
     * @param string[] $array The input array of strings to be converted.
     * @param string $prefix The prefix to be added to each line.
     * @param string $suffix The suffix to be added to each line.
     * @return string The resulting string with each array element prefixed, suffixed, and separated by a newline character.
     */
    public static function arrayToLineWithPrefixAndSuffix($array, $prefix, $suffix) {
        return implode("\n", array_map(function($item) use ($prefix, $suffix) {
            return $prefix . $item . $suffix;
        }, $array));
    }

    /**
     * Converts an array of strings into a single string, with each element prefixed by the '>' character.
     *
     * @param string[] $array The input array of strings to be converted.
     * @return string The resulting string with each array element prefixed by '>' and separated by a newline character.
     */
    public static function arrayToQuote($array) {
        return self::arrayToLineWithPrefix($array, "> ");
    }

    /**
     * Converts a string with newline characters into a quoted string, where each line is prefixed with the '>' character.
     *
     * @param string $string The input string to be converted.
     * @return string The resulting quoted string, with each line prefixed by '>'
     */
    public static function newlineToQuote($string) {
        return self::arrayToQuote(self::newlineToArray($string));
    }

    /**
     * Wraps the given text in bold markdown syntax.
     *
     * @param string $text The text to be wrapped in bold.
     * @return string The text wrapped in bold markdown syntax.
     */
    public static function bold($text) {
        return "**" . $text . "**";
    }

    /**
     * Wraps the given text in italic markdown syntax.
     *
     * @param string $text The text to be wrapped in italic.
     * @return string The text wrapped in italic markdown syntax.
     */
    public static function italic($text) {
        return "*" . $text . "*";
    }

    /**
     * Wraps the given text in strikethrough markdown syntax.
     *
     * @param string $text The text to be wrapped in strikethrough.
     * @return string The text wrapped in strikethrough markdown syntax.
     */
    public static function strikethrough($text) {
        return "~~" . $text . "~~";
    }

    /**
     * Wraps the given text in inline code markdown syntax.
     *
     * @param string $text The text to be wrapped in inline code.
     * @return string The text wrapped in inline code markdown syntax.
     */
    public static function code($text) {
        return "`" . $text . "`";
    }

    /**
     * Renders the given text as a code block with an optional language specifier.
     *
     * @param string $text The text to be rendered as a code block.
     * @param string $language An optional language specifier for the code block.
     * @return string The rendered code block.
     */
    public static function codeBlock($text, $language = "") {
        return "```" . $language . "\n" . $text . "\n```\n";
    }

    /**
     * Renders a Markdown link with the given text and URL.
     *
     * @param string $text The text to be displayed as the link.
     * @param string $url The URL that the link should point to.
     * @return string The Markdown-formatted link.
     */
    public static function link($text, $url) {
        return "[" . $text . "](" . $url . ")";
    }

    /**
     * Renders a Markdown image with the given alternative text and URL.
     *
     * @param string $altText The alternative text for the image.
     * @param string $url The URL of the image.
     * @return string The Markdown-formatted image.
     */
    public static function image($altText, $url) {
        return "![" . $altText . "](" . $url . ")";
    }

    /**
     * Renders an unordered list from the given array of items.
     *
     * @param array $items The items to be rendered as an unordered list.
     * @return string The rendered unordered list.
     */
    public static function unorderedList($items) {
        return self::arrayToLineWithPrefix($items, "- ");
    }

    /**
     * Renders the given items as an ordered list.
     *
     * @param array $items The items to be rendered as an ordered list.
     * @return string The rendered ordered list.
     */
    public static function orderedList($items) {
        return implode("\n", array_map(function($index, $item) {
            return ($index + 1) . ". " . $item;
        }, array_keys($items), $items));
    }

    /**
     * Renders a horizontal rule.
     *
     * @return string The rendered horizontal rule.
     */
    public static function horizontalRule() {
        return "---";
    }

    /**
     * Renders a Markdown table from the given headers and rows.
     *
     * @param string[] $headers The header cells for the table.
     * @param string[][] $rows The data rows for the table.
     * @return string The rendered Markdown table.
     */
    public static function table($headers, $rows) {
        // header
        $table  = "| " . implode(" | ", $headers) . " |\n"
                . "| " . implode(" | ", array_fill(0, count($headers), "---")) . " |\n";

        // rows
        foreach ($rows as $row) {
            $table .= "| " . implode(" | ", $row) . " |\n";
        }
        
        return $table;
    }
}