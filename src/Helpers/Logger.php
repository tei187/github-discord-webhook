<?php

namespace tei187\GitDisWebhook\Helpers;

/**
 * Provides a simple logging utility for the application.
 *
 * The `Logger` class provides a set of static methods for logging messages to a
 * log file. The log file is located at `logs/php-error.log` relative to the
 * application root directory.
 *
 * The `init()` method sets up the logging configuration, including enabling
 * error logging and setting the log file path.
 *
 * The `log()` method can be used to log messages of different log levels (e.g.
 * 'info', 'error', 'warning'). The log message is formatted with the current
 * timestamp and the specified log level.
 * 
 * **SAY ALL YOU WANT - this is THE debugging tool!**
 */
class Logger {
    
    /**
     * The path to the log file where log messages will be written.
     */
    const LOG_FILE = __DIR__ . '/../../logs/php-error.log';

    /**
     * Initializes the logging configuration for the application.
     *
     * This method sets up the logging environment by:
     * - Disabling the display of errors
     * - Enabling error logging
     * - Setting the log file path to the `php-error.log` file
     * - Enabling all error reporting levels
     *
     * This ensures that all errors, warnings, and notices are logged to the
     * specified log file, rather than being displayed on the web page.
     */
    public static function init() {
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', self::LOG_FILE);
        error_reporting(E_ALL);
    }

    /**
     * Logs a message with the specified log level.
     *
     * @param string $message The message to be logged.
     * @param string $level The log level, which can be 'info', 'error', 'warning', etc. Defaults to 'info'.
     */
    public static function log($message, $level = 'info') {
        $logFile = self::LOG_FILE;
        $logMessage = date('Y-m-d H:i:s') . " [$level] $message\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}