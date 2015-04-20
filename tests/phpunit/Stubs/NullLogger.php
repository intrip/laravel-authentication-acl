<?php  namespace LaravelAcl\Authentication\Tests\Unit\Stubs;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Illuminate\Contracts\Logging\Log as LogContract;

class NullLogger implements LogContract, PsrLoggerInterface {
    function __construct()
    {
        // do nothing...
    }

    public function __call($method, $parameters)
    {
        // do nothing...
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function alert($message, array $context = array())
    {
        // TODO: Implement alert() method.
    }

    /**
     * Log a critical message to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function critical($message, array $context = array())
    {
        // TODO: Implement critical() method.
    }

    /**
     * Log an error message to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function error($message, array $context = array())
    {
        // TODO: Implement error() method.
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function warning($message, array $context = array())
    {
        // TODO: Implement warning() method.
    }

    /**
     * Log a notice to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function notice($message, array $context = array())
    {
        // TODO: Implement notice() method.
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function info($message, array $context = array())
    {
        // TODO: Implement info() method.
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function debug($message, array $context = array())
    {
        // TODO: Implement debug() method.
    }

    /**
     * Log a message to the logs.
     *
     * @param  string $level
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        // TODO: Implement log() method.
    }

    /**
     * Register a file log handler.
     *
     * @param  string $path
     * @param  string $level
     * @return void
     */
    public function useFiles($path, $level = 'debug')
    {
        // TODO: Implement useFiles() method.
    }

    /**
     * Register a daily file log handler.
     *
     * @param  string $path
     * @param  int    $days
     * @param  string $level
     * @return void
     */
    public function useDailyFiles($path, $days = 0, $level = 'debug')
    {
        // TODO: Implement useDailyFiles() method.
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        // TODO: Implement emergency() method.
    }
}