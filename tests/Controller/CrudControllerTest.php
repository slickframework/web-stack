<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Controller;

use Slick\Form\FormInterface;
use Slick\Form\FormRegistry;
use Slick\Mvc\Controller\CrudController;
use Slick\Mvc\Form\EntityForm;

/**
 * Crud Controller composition test case
 * 
 * @package Slick\Tests\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CrudControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Should have no collisions or strict errors
     */
    public function testCrudController()
    {
        $this->assertInstanceOf(CrudController::class, new Posts);
    }
    
}

/**
 * CRUD composite Posts controller
 * 
 * @package Slick\Tests\Mvc\Controller
 */
class Posts extends CrudController
{


    /**
     * Gets the URL base path form this controller
     *
     * @return string
     */
    protected function getBasePath()
    {
        return;
    }

    /**
     * @return FormInterface|EntityForm
     */
    function getForm()
    {
        FormRegistry::getForm(__DIR__.'/Form/postsForm.yml');
        return;
    }

    /**
     * Gets the entity FQ class name
     *
     * @return string
     */
    public function getEntityClassName()
    {
        return;
    }
}