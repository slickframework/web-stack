<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service\Entity;

use Slick\Mvc\Exception\Service\InvalidFormDataException;
use Slick\Mvc\Form\EntityForm;
use Slick\Mvc\Service\EntityServiceInterface;
use Slick\Orm\Entity;

/**
 * Entity Update Service
 * 
 * @package Slick\Mvc\Service\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityUpdateService extends AbstractEntityService implements
    EntityServiceInterface
{

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var EntityForm
     */
    protected $form;
    
    /**
     * Entity Update Service need an entity.
     * 
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->setEntity($entity);
    }

    /**
     * Updates entity with provided data
     */
    public function update()
    {
        $this->entity->save($this->getData());
    }

    /**
     * Get current data to be updated
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data for entity update
     * 
     * @param array $data
     * 
     * @return EntityUpdateService
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get entity for
     * 
     * @return EntityForm
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set entity for
     * 
     * @param EntityForm $form
     * 
     * @return EntityUpdateService
     * 
     * @throws InvalidFormDataException If the provided form has invalid data
     */
    public function setForm(EntityForm $form)
    {
        if (!$form->isValid()) {
            throw new InvalidFormDataException(
                "Submitted form data is not valid."
            );
        }
        $this->setData($form->getData());
        $this->form = $form;
        return $this;
    }
    
}