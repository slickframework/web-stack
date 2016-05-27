<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mvc;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Slick\Http\PhpEnvironment\Response;
use Slick\Http\Server;
use Slick\Http\Server\Request;
use Slick\Mvc\Application;
use PHPUnit_Framework_Assert as Assert;

/**
 * Step definitions for slick/session package
 *
 * @package Mvc
 * @behatContext
 */
class MvcContext extends MinkContext implements
    Context, SnippetAcceptingContext
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Response
     */
    protected $response;

    /**
     * MvcContext constructor
     */
    public function __construct()
    {
        $this->application = new Application();
        $this->application->setConfigPath(dirname(__DIR__).'/App/Configuration');
    }

    /**
     * Start up the web server
     *
     * @BeforeSuite
     *
     * @param BeforeSuiteScope $scope
     */
    public static function setUp(BeforeSuiteScope $scope)
    {
        $params = $scope->getSuite()->getSetting('phpwebserver');
        PhpServer::start($params);
    }

    /**
     * Kill the httpd process if it has been started when the tests have finished
     * 
     * @AfterSuite
     *
     * @param AfterSuiteScope $scope
     */
    public static function tearDown(AfterSuiteScope $scope)
    {
        PhpServer::stop();
    }

    /**
     * @Given /^I request page "([^"]*)"$/
     * @param $path
     */
    public function request($path)
    {
        $request = (new Request(Request::METHOD_GET))
            ->withQueryParams(['url' => $path]);
        $this->application->setRunner($this->getServer($request));
        $this->response = $this->application->getResponse();
    }

    /**
     * @Then /^response should contain "([^"]*)"$/
     * @Then /^response should contain \'([^\']*)\'$/
     * @param $text
     */
    public function containsText($text)
    {
        $normalised = "#$text#i";
        Assert::assertRegExp($normalised, $this->response->getBody()->__toString());
    }
    
    protected function getServer(Request $request)
    {
        $server = new Server($request);
        $container = $this->application->getContainer();
        $server
            ->add($container->get('url.rewrite.middleware'))
            ->add($container->get('router.middleware'))
            ->add($container->get('dispatcher.middleware'))
            ->add($container->get('renderer.middleware'))
        ;
        return $server;
    }

}