<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack;

use Psr\Http\Message\ResponseInterface;
use Slick\Di\ContainerBuilder;
use Slick\Di\ContainerInterface;
use Slick\Http\PhpEnvironment\Response;
use Slick\Http\Server;

/**
 * Application
 *
 * @package Slick\WebStack
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Application
{

    /**
     * @var string
     */
    private $servicesPath;

    /**
     * @var Server
     */
    private $httpServer;

    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * Creates ab MVC Application
     *
     * @param string|null $servicesPath
     */
    public function __construct($servicesPath = null)
    {
        $this->servicesPath = $servicesPath;
        $this->createContainer();
    }

    /**
     * Get application dependency container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return self::$container;
    }

    /**
     * Get HTTP middleware server
     *
     * @return Server
     */
    public function getHttpServer()
    {
        if (!$this->httpServer) {
            $server = $this->getContainer()->get('middleware.server');
            $this->setHttpServer($server);
        }
        return $this->httpServer;
    }

    /**
     * Set the HTTP middleware server
     *
     * @param Server $httpServer
     *
     * @return Application
     */
    public function setHttpServer(Server $httpServer)
    {
        $this->httpServer = $httpServer;
        return $this;
    }

    /**
     * @return ResponseInterface|Response
     */
    public function run()
    {
        return $this->getHttpServer()->run();
    }

    /**
     * Creates the default dependency container
     */
    private function createContainer()
    {
        $this->createApplicationContainer();
        $builder = new ContainerBuilder(__DIR__.'/Service/Definitions');
        self::$container = $builder->getContainer();
    }

    /**
     * Creates user defined container
     *
     * @return Application
     */
    private function createApplicationContainer()
    {
        if ($this->servicesPath === null) {
            return $this;
        }

        $builder = new ContainerBuilder($this->servicesPath);
        $builder->getContainer();
        return $this;
    }
}