<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service;

use Slick\Orm\EntityInterface;

/**
 * Entity Service Interface
 * 
 * @package Slick\Mvc\Service
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EntityServiceInterface
{

    /**
     * Set FQ entity class name
     * 
     * This method MUST verify if the provided class name implements the
     * Slick\Orm\EntityInterface and if it does not implements it an exception
     * SHOULD be thrown.
     * 
     * @param string $className
     *
     * @throws ServiceException If the provided class name does not implements
     * the Slick\Orm\EntityInterface interface.
     *
     * @return $this|self|EntityServiceInterface
     */
    public function setEntityClass($className);

    /**
     * Sets the entity that will be the subject of this service
     *
     * @param EntityInterface $entity
     *
     * @return $this|self|EntityServiceInterface
     */
    public function setEntity(EntityInterface $entity);
}