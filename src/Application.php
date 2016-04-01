<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc;

use Interop\Container\ContainerInterface;
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
    }

    /**
     * Gets the application dependency injection container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if (is_null(self::$container)) {
            self::$container = (
            new ContainerBuilder($this->configPath.'/services.php')
            )
                ->getContainer();
        }
        return self::$container;
    }

    /**
     * Sets the dependency injection container
     *
     * @param ContainerInterface $container
     *
     * @return $this|self|Application
     */
    public function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
        return $this;
    }

    /**
     * Returns the application dependency container
     *
     * @return ContainerInterface
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
            $this->setRunner(
                $this->getContainer()
                    ->get('middleware.runner')
            );
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
        }
        return $this->configPath;
    }

}