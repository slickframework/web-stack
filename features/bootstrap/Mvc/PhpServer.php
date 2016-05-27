<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvc;

use RuntimeException;

/**
 * PHP Built in Server
 * 
 * @package Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class PhpServer
{

    /**
     * Pid for the web server
     *
     * @var int
     */
    private static $pid;

    /**
     * Start up the web server
     *
     * Parameters are:
     *  - url
     *  - documentRoot
     *  - timeout
     *
     * @param array $params
     */
    public static function start(array $params)
    {
        // Fetch config
        $url = parse_url($params['url']);
        $port = !empty($url['port']) ? $url['port'] : 80;

        if (self::canConnectToHttpd($url['host'], $port)) {
            throw new RuntimeException(
                "Something is already running on {$params['url']}. Aborting tests."
            );
        }

        // Try to start the web server
        self::$pid = self::startBuiltInHttpd(
            $url['host'],
            $port,
            $params['documentRoot']
        );

        if (!self::$pid) {
            throw new RuntimeException('Could not start the web server');
        }

        $start = microtime(true);
        $connected = false;

        // Try to connect until the time spent exceeds the timeout specified in the configuration
        while (microtime(true) - $start <= (int) $params['timeout']) {
            if (self::canConnectToHttpd($url['host'], $port)) {
                $connected = true;
                break;
            }
        }
        
        if (!$connected) {
            self::killProcess(self::$pid);
            throw new RuntimeException(
                sprintf(
                    'Could not connect to the web server within the given timeframe (%d second(s))',
                    $params['timeout']
                )
            );
        }

    }

    /**
     * Kill the httpd process if it has been started
     */
    public static function stop()
    {
        if (self::$pid) {
            self::killProcess(self::$pid);
        }
    }

    /**
     * Kill a process
     *
     * @param int $pid
     */
    private static function killProcess($pid)
    {
        exec('kill ' . (int) $pid);
    }

    /**
     * See if we can connect to the httpd
     *
     * @param string $host The hostname to connect to
     * @param int $port The port to use
     * @return boolean
     */
    private static function canConnectToHttpd($host, $port)
    {
        // Disable error handler for now
        set_error_handler(function() { return true; });
        // Try to open a connection
        $sp = fsockopen($host, $port);
        // Restore the handler
        restore_error_handler();
        if ($sp === false) {
            return false;
        }
        fclose($sp);
        return true;
    }

    /**
     * Start the built in Httpd
     *
     * @param string $host The hostname to use
     * @param int $port The port to use
     * @param string $documentRoot The document root
     *
     * @return int Returns the PID of the httpd
     */
    private static function startBuiltInHttpd($host, $port, $documentRoot)
    {
        // $path = dirname(dirname(dirname(__DIR__))).'/build/php-server.log';
        $path = '/dev/null';
        // Build the command
        $command = sprintf('php -S %s:%d -t %s > %s 2>&1 & echo $!',
            $host,
            $port,
            $documentRoot,
            $path
        );
        $output = array();
        $out = exec($command, $output);
        return (int) $output[0];
    }

}