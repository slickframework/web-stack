<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Service\Entity;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Form\FormRegistry;
use Slick\Mvc\Exception\Service\InvalidFormDataException;
use Slick\Mvc\Form\EntityForm;
use Slick\Mvc\Service\Entity\EntityUpdateService;
use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

/**
 * Entity Update Service Test Case
 *
 * @package Slick\Tests\Mvc\Service\Entity
 */
class EntityUpdateServiceTest extends TestCase
{

    /**
     * @var EntityUpdateService
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        /** @var Entity $entity */
        $entity = $this->getMock(Entity::class);
        $this->service = new EntityUpdateService($entity);
    }

    public function testSetForm()
    {
        /** @var EntityForm $form */
        $form = FormRegistry::getForm(
            dirname(__DIR__).'/../Form/testForm.yml'
        );
        $form->setData(['id' => 1]);
        $this->service->setForm($form);
        $this->assertEquals(['id' => 1], $this->service->getData());
        $this->assertSame($form, $this->service->getForm());
    }

    public function testSetInvalidForm()
    {
        /** @var EntityForm|MockObject $form */
        $form = $this->getMockBuilder(EntityForm::class)
            ->setMethods(['isValid'])
            ->disableOriginalConstructor()
            ->getMock();
        $form->method('isValid')
            ->willReturn(false);
        $this->setExpectedException(InvalidFormDataException::class);
        $this->service->setForm($form);
    }
    
    public function testUpdate()
    {
        /** @var EntityInterface|MockObject $entity */
        $entity = $this->getMock(EntityInterface::class);
        $entity->expects($this->once())
            ->method('save')
            ->with($this->isType('array'));
        $this->service->setEntity($entity);
        $this->service->update();
    }
    
}
