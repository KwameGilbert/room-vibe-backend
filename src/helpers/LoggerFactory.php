<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerFactory
{
    // Array to hold logger instances, keyed by channel name.
    private static $loggers = [];

    /**
     * Get a logger instance for the specified channel.
     *
     * @param string $channel The logging channel name (default is 'app').
     * @return Logger A Monolog Logger instance.
     */
    public static function getLogger(string $channel = 'app'): Logger
    {
        // Return the logger if it has already been created.
        if (!isset(self::$loggers[$channel])) {
            // Create a new logger for the specified channel.
            $logger = new Logger($channel);

            // Define the path for the log file. Adjust the path if needed.
            $logFile = __DIR__ . '/../logs/app.log';

            // Create a StreamHandler that writes log entries to the file.
            // The log level is set to DEBUG so that all log levels are recorded.
            $logger->pushHandler(new StreamHandler($logFile, Logger::toMonologLevel($_ENV['LOG_LEVEL'] ?: 'DEBUG')));

            // Cache the logger instance.
            self::$loggers[$channel] = $logger;
        }
        return self::$loggers[$channel];
    }
}
