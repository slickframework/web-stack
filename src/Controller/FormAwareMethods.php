<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Slick\Form\FormInterface;
use Slick\Mvc\Form\EntityForm;
use Slick\Mvc\Service\Entity\EntityUpdateService;
use Slick\Orm\Entity;

/**
 * Form Aware Methods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait FormAwareMethods
{

    /**
     * @var EntityUpdateService
     */
    protected $updateService;

    /**
     * @return FormInterface|EntityForm
     */
    abstract function getForm();

    /**
     * Get invalid form data message
     *
     * @return string
     */
    protected function getInvalidFormDataMessage()
    {
        $singleName = $this->getEntityNameSingular();
        $message = "The {$singleName} could not be saved. Please check the " .
            "errors and try again.";
        return $this->translate($message);
    }

    /**
     * Get invalid form data message
     *
     * @param \Exception $caught
     *
     * @return string
     */
    protected function getGeneralErrorMessage(\Exception $caught)
    {
        $singleName = $this->getEntityNameSingular();
        $message = "There was an error when saving {$singleName} data: %s";
        return sprintf($this->translate($message), $caught->getMessage());
    }

    /**
     * Get entity singular name used on controller actions
     *
     * @return string
     */
    abstract protected function getEntityNameSingular();

    /**
     * Get update service
     *
     * @return EntityUpdateService
     */
    public function getUpdateService()
    {
        if (null == $this->updateService) {
            $this->setUpdateService(
                new EntityUpdateService($this->getNewEntity())
            );
        }
        return $this->updateService;
    }

    /**
     * Set update service
     *
     * @param EntityUpdateService $updateService
     *
     * @return EntityCreateMethods
     */
    public function setUpdateService(EntityUpdateService $updateService)
    {
        $this->updateService = $updateService;
        return $this;
    }

    /**
     * Get a new entity
     *
     * @return Entity
     */
    protected function getNewEntity()
    {
        $class = $this->getEntityClassName();
        return new $class();
    }

    /**
     * Gets the entity FQ class name
     *
     * @return string
     */
    abstract public function getEntityClassName();

    /**
     * Returns the translation for the provided message
     *
     * @param string $message
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    abstract public function translate(
        $message, $domain = null, $locale = null
    );
}