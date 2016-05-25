<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Form\FormRegistry;
use Slick\Mvc\Form\EntityForm;

/**
 * Entity Form Test Case
 * 
 * @package Slick\Tests\Mvc\Form
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityFormTest extends TestCase
{

    /**
     * @var EntityForm
     */
    private $form;

    /**
     * Set the SUT form object
     */
    protected function setup()
    {
        parent::setUp();
        $this->form = FormRegistry::getForm(__DIR__ . '/testForm.yml');
    }

    /**
     * Should not return the form id element
     * @test
     */
    public function getData()
    {
        $data = ['id' => 2];
        $this->form->setData($data);
        $this->assertEquals($data, $this->form->getData());
        
    }
}
