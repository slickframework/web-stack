<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Service\Entity;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Exception\Service\InvalidEntityClassException;
use Slick\Mvc\Exception\Service\MissingEntityException;
use Slick\Mvc\Service\Entity\AbstractEntityService;
use Slick\Tests\Mvc\Fixtures\Domain\Post;

/**
 * Abstract Entity Service Test Case
 * 
 * @package Slick\Tests\Mvc\Service\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AbstractEntityServiceTest extends TestCase
{

    /**
     * @var AbstractEntityService
     */
    protected $service;

    /**
     * Sets the SUT service object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->getMockForAbstractClass(
            AbstractEntityService::class
        );
    }

    /**
     * Should trow an exception if provided class does not implements the
     * Slick\Orm\EntityInterface interface
     * @test
     */
    public function setInvalidClassName()
    {
        $this->setExpectedException(InvalidEntityClassException::class);
        $this->service->setEntityClass('stdClass');
    }

    /**
     * Should set the class name (lazy mode) based on the entity object
     * @test
     */
    public function getClassNameFromEntity()
    {
        $post = new Post(['id' => 243]);
        $this->service->setEntity($post);
        $this->assertEquals(Post::class, $this->service->getEntityClassName());
    }

    /**
     * Should throw an exception if no entity is set for the service.
     * There should not exists an entity service without an entity or
     * entity FQ class name.
     * @test
     */
    public function getMissingEntity()
    {
        $this->setExpectedException(MissingEntityException::class);
        /** @var AbstractEntityService $service */
        $service = $this->getMockForAbstractClass(
            AbstractEntityService::class
        );
        $service->getEntity();
    }
}
