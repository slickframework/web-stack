<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\Command\Task;

use Slick\Mvc\Console\MetaDataGenerator\Controller;
use Slick\Mvc\Console\MetaDataGenerator\Entity;
use Slick\Mvc\Exception\Console\EntityClassNotFound;

/**
 * CreatedCrudController
 *
 * @package Slick\Mvc\Console\Command\Task
 * @author  Filipe Silva <silva.filipe@gmail.com>
 *
 * @property string $entityName
 */
class CreateCrudController extends CreateController
{

    /**
     * @readwrite
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $template = 'crud-controller.twig';

    /**
     * Configures the controller generator
     *
     * @param Controller $controller
     */
    protected function configureController(Controller $controller)
    {
        parent::configureController($controller);
        $controller[] = new Entity($this->entityName);
    }

    /**
     * Sets the entity class name
     *
     * @param $entityName
     *
     * @return self|CreateCrudController
     */
    public function setEntityName($entityName)
    {
        if (!class_exists($entityName)) {
            throw new EntityClassNotFound(
                "The entity class '{$entityName}' does not exist in your system."
            );
        }
        $this->entityName = $entityName;
        return $this;
    }
}