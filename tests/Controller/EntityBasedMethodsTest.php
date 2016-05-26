<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Controller;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\I18n\TranslateMethods;
use Slick\Mvc\Controller\EntityBasedMethods;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Exception\Service\EntityNotFoundException;
use Slick\Orm\Repository\EntityRepository;
use Slick\Tests\Mvc\Fixtures\Domain\Post;

/**
 * Entity Based Methods Test
 *
 * @package Slick\Tests\Mvc\Controller
 */
class EntityBasedMethodsTest extends TestCase
{

    use EntityBasedMethods;
    
    use TranslateMethods;

    public function testGetEntity()
    {
        $this->repository = $this->getRepositoryWithPost();
        $post = $this->getEntity(33);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testGetNullEntity()
    {
        $this->repository = $this->getRepositoryWithNothing();
        $this->setExpectedException(EntityNotFoundException::class);
        $this->getEntity(33);
    }
    
    private function getRepositoryWithPost()
    {
        /** @var EntityRepository|MockObject $repository */
        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $repository->expects($this->once())
            ->method('get')
            ->willReturn(new Post(['id' => 33]));
        return $repository;
    }
    
    private function getRepositoryWithNothing()
    {
        /** @var EntityRepository|MockObject $repository */
        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $repository->expects($this->once())
            ->method('get')
            ->willReturn(null);
        return $repository;
    }

    /**
     * Gets updated HTTP request
     *
     * @return ServerRequestInterface|Request
     */
    public function getRequest()
    {
        return null;
    }

    /**
     * Sets a value to be used by render
     *
     * The key argument can be an associative array with values to be set
     * or a string naming the passed value. If an array is given then the
     * value will be ignored.
     *
     * Those values must be set in the request attributes so they can be used
     * latter by any other middle ware in the stack.
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return ControllerInterface
     */
    public function set($key, $value = null)
    {
        return $this;
    }

    /**
     * Gets the entity FQ class name
     *
     * @return string
     */
    public function getEntityClassName()
    {
        return Post::class;
    }

    /**
     * Redirects the flow to another route/path
     *
     * @param string $path the route or path to redirect to
     *
     * @return ControllerInterface|self|$this
     */
    public function redirect($path)
    {
        return $this;
    }

    /**
     * Gets the URL base path form this controller
     *
     * @return string
     */
    protected function getBasePath()
    {
        return 'posts';
    }
}
