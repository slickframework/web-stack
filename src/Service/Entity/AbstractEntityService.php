<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service\Entity;

use Slick\Mvc\Exception\Service\InvalidEntityClassException;
use Slick\Mvc\Exception\Service\MissingEntityException;
use Slick\Mvc\Service\EntityServiceInterface;
use Slick\Orm\EntityInterface;

/**
 * Base Entity Service
 * 
 * @package Slick\Mvc\Service\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class AbstractEntityService implements EntityServiceInterface
{

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * Set FQ entity class name
     *
     * @param string $className
     *
     * @throws InvalidEntityClassException If the provided class name does
     * not implements the Slick\Orm\EntityInterface interface.
     *
     * @return $this|self|AbstractEntityService
     */
    public function setEntityClass($className)
    {
        if (
            !class_exists($className) ||
            !is_subclass_of($className, EntityInterface::class)
        ) {
            throw new InvalidEntityClassException(
                "Class '{$className}' does not implements the " .
                "Slick\Orm\EntityInterface interface."
            );
        }
        
        $this->entityClass = $className;
        return $this;
    }

    /**
     * Sets the entity that will be the subject of this service
     *
     * @param EntityInterface $entity
     *
     * @return $this|self|AbstractEntityService
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get the entity FQ class name
     * 
     * @return string
     */
    public function getEntityClassName()
    {
        if (null == $this->entityClass) {
            $this->setEntityClass(get_class($this->getEntity()));
        }
        return $this->entityClass;
    }

    /**
     * Gets entity class
     * 
     * @return EntityInterface
     */
    public function getEntity()
    {
        if (null == $this->entity) {
            throw new MissingEntityException(
                "There are no Entity object set for this service."
            );
        }
        return $this->entity;
    }
}