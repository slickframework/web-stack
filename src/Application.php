<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc;

use Interop\Container\ContainerInterface;
use Slick\Configuration\Configuration;
use Slick\Di\Container;
use Slick\Di\ContainerBuilder;
use Slick\Http\PhpEnvironment\MiddlewareRunnerInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;

/**
 * Class Application
 * @package Slick\Mvc
 */
class Application
{

    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MiddlewareRunnerInterface
     */
    private $runner;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var ContainerInterface
     */
    private static $defaultContainer;

    /**
     * Creates an application with an HTTP request
     *
     * If no request is provided then a Slick\Http\PhpEnvironment\Request is
     * created with values form current php environment settings.
     *
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request;
        Configuration::addPath(__DIR__.'/Configuration');
        $definitions = include __DIR__.'/Configuration/services.php';
        self::$defaultContainer = (
            new ContainerBuilder($definitions)
        )
        ->getContainer();
    }

    /**
     * Sets a new request
     *
     * @param Request $request
     *
     * @return Application
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        $this->getContainer()->register('request', $request);
        return $this;
    }

    /**
     * Gets the application dependency injection container
     *
     * @return ContainerInterface|Container
     */
    public function getContainer()
    {
        if (is_null(self::$container)) {
            self::$container = $this->checkContainer();
        }
        return self::$container;
    }

    /**
     * Sets the dependency injection container
     *
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * Returns the application dependency container
     *
     * @return ContainerInterface|Container
     */
    public static function container()
    {
        if (null == self::$container) {
            $app = new static;
            $app->getContainer();
        }
        return self::$container;
    }

    /**
     * Set middleware runner
     *
     * @param MiddlewareRunnerInterface $runner
     *
     * @return $this|self|Application
     */
    public function setRunner(MiddlewareRunnerInterface $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * Returns the processed response
     *
     * @return Response
     */
    public function getResponse()
    {
        if (is_null($this->response)) {
            $this->response = $this->getRunner()->run();
        }
        return $this->response;
    }

    /**
     * Gets the HTTP middleware runner for this application
     *
     * @return MiddlewareRunnerInterface
     */
    public function getRunner()
    {
        if (null === $this->runner) {
            $runner = $this->getContainer()
                ->get('middleware.runner')
                ->setRequest($this->getRequest())
            ;
            $this->setRunner($runner);
        }
        return $this->runner;
    }

    /**
     * Set configuration path
     * 
     * @param string $configPath
     * 
     * @return Application|self
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
        Configuration::addPath($configPath);
        return $this;
    }

    /**
     * Gets configuration path
     * 
     * @return string
     */
    public function getConfigPath()
    {
        if (null == $this->configPath) {
            $this->configPath = getcwd().'/Configuration';
            Configuration::addPath($this->configPath);
        }
        return $this->configPath;
    }

    /**
     * Gets HTTP request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = $this->getContainer()
                ->get('request');
        }
        return $this->request;
    }

    /**
     * Gets container with user overridden settings
     * 
     * @return ContainerInterface|\Slick\Di\Container
     */
    protected function checkContainer()
    {
        $container = self::$defaultContainer;
        if (
            null != $this->configPath &&
            file_exists($this->configPath.'/services.php')
        ) {
            $definitions = include $this->configPath.'/services.php';
            $container = (
                new ContainerBuilder(
                    $definitions,
                    true
                )
            )->getContainer();
        }
        return $container;
    }

}