<?php
/*
* Logger class
*
* This is used for logging in the system
*
* @author Ken Lalobo
*
*/

namespace Mooti\Xizlr\Core;

use \Psr\Log\LoggerInterface;
use \Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    private $requestId;
    private $requestTime;
    private $appName;

    private $allowedLogLevels = array(
        LogLevel::EMERGENCY => LOG_EMERG,
        LogLevel::ALERT     => LOG_ALERT,
        LogLevel::CRITICAL  => LOG_CRIT,
        LogLevel::ERROR     => LOG_ERR,
        LogLevel::WARNING   => LOG_WARNING,
        LogLevel::NOTICE    => LOG_NOTICE,
        LogLevel::INFO      => LOG_INFO,
        LogLevel::DEBUG     => LOG_DEBUG
    );

    public function __construct()
    {
        $this->requestId   = \Mooti\Xizlr\Core\Util::uuidV4();
        $this->requestTime = new \DateTime();
        $this->appName     = 'mooti';
    }

    /**
     * Set the app name. This helps in identifying log entries for this application
     *
     * @param string $appName
     * @return null
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * Set the request id. This helps in identifying related log entries
     *
     * @param string $requestId
     * @return null
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Set the request time.
     *
     * @param string $requestId
     * @return null
     */
    public function setRequestTime(\DateTime $requestTime)
    {
        $this->requestTime = $requestTime;
    }

     /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function emergency($message, array $context = array())
    {
        return $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function alert($message, array $context = array())
    {
        return $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function critical($message, array $context = array())
    {
        return $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function error($message, array $context = array())
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function warning($message, array $context = array())
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function notice($message, array $context = array())
    {
        return $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function info($message, array $context = array())
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function debug($message, array $context = array())
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return boolean
     */
    public function log($level, $message, array $context = array())
    {
        if (empty($this->allowedLogLevels[$level]) == false) {
            $backTrace = debug_backtrace();

            $function = (empty($backTrace[2]) == false?$backTrace[2]['function']:$backTrace[1]['function']);
            $class    = (empty($backTrace[2]) == false?$backTrace[2]['class']:$backTrace[1]['class']);
            $line     = (empty($backTrace[2]) == false?$backTrace[2]['line']:$backTrace[1]['line']);

            $data = array(
                'requestId'   => $this->requestId,
                'requestTime' => $this->requestTime->format('r'),
                'level'       => $level,
                'message'     => $message,
                'context'     => $context,
                'location'    => array(
                    'function' => $function,
                    'class'    => $class,
                    'line'     => $line
                )
            );

            $logMessage = $this->appName.'/xizlr['.getmypid().']: '.json_encode($data);

            syslog($this->allowedLogLevels[$level], $logMessage);

            return true;
        }

        return false;
    }
}
