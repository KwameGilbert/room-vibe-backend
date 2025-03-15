<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerFactory
{
    // Property to hold the logger instance.
    private $logger;

    /**
     * Constructor to initialize the logger for the specified channel.
     *
     * @param string $channel The logging channel name (default is 'app').
     */
    public function __construct(string $channel = 'app')
    {
        // Create a new logger for the specified channel.
        $this->logger = new Logger($channel);

        // Define the path for the log file. Adjust the path if needed.
        $logFile = __DIR__ . '/../logs/app.log';

        // Create a StreamHandler that writes log entries to the file.
        // The log level is set to DEBUG so that all log levels are recorded.
        $logLevel = isset($_ENV['LOG_LEVEL']) ? $_ENV['LOG_LEVEL'] : 'DEBUG';
        $this->logger->pushHandler(new StreamHandler($logFile, Logger::toMonologLevel($logLevel)));
    }

    /**
     * Get the logger instance.
     *
     * @return Logger A Monolog Logger instance.
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }
}