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

/**
 * Entity View Methods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EntityViewMethods
{

    /**
     * For entity retrieval
     */
    use EntityBasedMethods;
    
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
        return "The {$singleName} with ID {$entityId} was not found.";
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
     */
    public function show($entityId = 0)
    {
        $entityId = StaticFilter::filter('text', $entityId);
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
        
    }
}