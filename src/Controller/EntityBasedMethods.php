<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Slick\Mvc\Exception\Service\EntityNotFoundException;
use Slick\Orm\EntityInterface;
use Slick\Orm\Orm;
use Slick\Orm\RepositoryInterface;

/**
 * Class EntityBasedMethods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EntityBasedMethods
{

    /**
     * For basic entity metadata
     */
    use CrudCommonMethods;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

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
        $entity = $this->getRepository()->get($entityId);
        if (!$entity instanceof EntityInterface) {
            throw new EntityNotFoundException(
                "There are no entities with provided entity ID."
            );
        }
        return $entity;
    }

    /**
     * Get entity repository
     * 
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        if (null == $this->repository) {
            $this->setRepository(
                Orm::getRepository($this->getEntityClassName())
            );
        }
        return $this->repository;
    }

    /**
     * Set entity repository
     * 
     * @param RepositoryInterface $repository
     * 
     * @return self|$this|EntityBasedMethods
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Get the current entity descriptor
     *
     * @return \Slick\Orm\Descriptor\EntityDescriptorInterface
     */
    protected function getEntityDescriptor()
    {
        return $this->getRepository()
            ->getEntityDescriptor();
    }

    /**
     * Gets the URL base path form this controller
     * 
     * @return string
     */
    abstract protected function getBasePath();
}