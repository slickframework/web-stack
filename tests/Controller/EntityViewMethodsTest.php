<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\I18n\TranslateMethods;
use Slick\Mvc\Controller\EntityBasedMethods;
use Slick\Mvc\Controller\EntityViewMethods;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Exception\Service\EntityNotFoundException;
use Slick\Orm\EntityInterface;
use Slick\Tests\Mvc\Fixtures\Domain\Post;

/**
 * Entity View Methods Test Case
 *
 * @package Slick\Tests\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityViewMethodsTest extends TestCase
{

    use EntityBasedMethods;

    use EntityViewMethods;
    
    use TranslateMethods;

    private $vars = [];

    private $warningMessage;

    private $path;
    
    private $entity;

    /**
     * Should put an entity in the 'post' view vars
     * @test
     */
    public function showNormalEntity()
    {
        $this->entity = new Post(['id' => 33]);
        $this->show(33);
        $this->assertSame($this->vars['post'], $this->entity);
    }

    /**
     * Should redirect to the index page with a warning message
     * @test
     */
    public function showEmptyEntity()
    {
        $this->entity = null;
        $this->show(34);
        $this->assertEquals('posts', $this->path);
        $this->assertTrue(is_string($this->warningMessage));
    }

    /**
     * Gets entity with provided primary key
     *
     * @param mixed $entityId
     *
     * @return EntityInterface
     *
     * @throws EntityNotFoundException If no entity was found with
     *   provided primary key
     */
    protected function getEntity($entityId)
    {
        if ($entityId == "34") {
            throw new EntityNotFoundException("Test");
        }
        return $this->entity;
    }
    
    /**
     * Gets updated HTTP request
     *
     * @return ServerRequestInterface|Request
     */
    public function getRequest()
    {
        return new Request();
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
        if (is_array($key)) {
            $this->vars = array_merge($this->vars, $key);
            return $this;
        }
        $this->vars[$key] = $value;
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
        $this->path = $path;
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

    /**
     * Add a warning flash message
     *
     * @param string $message
     * @return self
     */
    public function addWarningMessage($message)
    {
        $this->warningMessage = $message;
        return $this;
    }
}
