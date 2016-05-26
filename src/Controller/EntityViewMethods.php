<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Slick\Common\Log;
use Slick\Filter\StaticFilter;
use Slick\Mvc\Exception\Service\EntityNotFoundException;
use Slick\Orm\EntityInterface;

/**
 * Entity View Methods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EntityViewMethods
{

    /**
     * Add a warning flash message
     *
     * @param string $message
     * @return self
     */
    abstract public function addWarningMessage($message);

    /**
     * Get missing entity warning message
     * 
     * @param mixed $entityId
     * 
     * @return string
     */
    protected function getMissingEntityMessage($entityId)
    {
        $singleName = $this->getEntityNameSingular();
        $message = "The {$singleName} with ID %s was not found.";
        return sprintf($this->translate($message), $entityId);
    }

    /**
     * Redirect callback after missing entity detection
     * 
     * @return $this|\Slick\Mvc\ControllerInterface|static
     */
    protected function redirectFromMissingEntity()
    {
        return $this->redirect($this->getBasePath());
    }

    /**
     * Handles the request to view an entity
     *
     * @param int $entityId
     * 
     * @return null|EntityInterface
     */
    public function show($entityId = 0)
    {
        $entityId = StaticFilter::filter('text', $entityId);
        $entity = null;
        try {
            $entity = $this->getEntity($entityId);
            $this->set($this->getEntityNameSingular(), $entity);
        } catch (EntityNotFoundException $caught) {
            Log::logger()->addNotice($caught->getMessage());
            $this->addWarningMessage(
                $this->getMissingEntityMessage($entityId)
            );
            $this->redirectFromMissingEntity();
        }
        return $entity;
    }

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
    abstract protected function getEntity($entityId);

    /**
     * Get entity singular name used on controller actions
     *
     * @return string
     */
    abstract protected function getEntityNameSingular();

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