<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Slick\Common\Log;
use Slick\Form\FormInterface;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Exception\Service\InvalidFormDataException;
use Slick\Mvc\Form\EntityForm;
use Slick\Mvc\Service\Entity\EntityUpdateService;
use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

/**
 * Entity Create Methods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EntityCreateMethods
{

    /**
     * Handle the add entity request
     */
    public function add()
    {
        $form = $this->getForm();
        $this->set(compact('form'));
        
        if (!$form->wasSubmitted()) {
            return;
        }
        
        try {
            $this->getUpdateService()
                ->setForm($form)
                ->update();
            ;
        } catch (InvalidFormDataException $caught) {
            Log::logger()->addNotice($caught->getMessage(), $form->getData());
            $this->addErrorMessage($this->getInvalidFormDataMessage());
            return;
        } catch (\Exception $caught) {
            Log::logger()->addCritical(
                $caught->getMessage(),
                $form->getData()
            );
            $this->addErrorMessage($this->getGeneralErrorMessage($caught));
            return;
        }
        
        $this->addSuccessMessage(
            $this->getCreateSuccessMessage($this->getUpdateService()->getEntity())
        );
    }
    
    /**
     * Get the create successful entity message
     * 
     * @param EntityInterface $entity
     * 
     * @return string
     */
    protected function getCreateSuccessMessage(EntityInterface $entity)
    {
        $singleName = $this->getEntityNameSingular();
        $message = "The {$singleName} '%s' was successfully created.";
        return sprintf($this->translate($message), $entity);
    }

    /**
     * Get update service
     *
     * @return EntityUpdateService
     */
    abstract public function getUpdateService();

    /**
     * @return FormInterface|EntityForm
     */
    abstract function getForm();

    /**
     * Get invalid form data message
     *
     * @param \Exception $caught
     *
     * @return string
     */
    abstract protected function getGeneralErrorMessage(\Exception $caught);

    /**
     * Get invalid form data message
     *
     * @return string
     */
    abstract protected function getInvalidFormDataMessage();

    /**
     * Sets a value to be used by render
     *
     * The key argument can be an associative array with values to be set
     * or a string naming the passed value. If an array is given then the
     * value will be ignored.
     *
     * Those values must be set in the request attributes so they can be used
     * latter by any other middle ware in the stack.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return ControllerInterface
     */
    abstract public function set($key, $value = null);

    /**
     * Redirects the flow to another route/path
     *
     * @param string $path the route or path to redirect to
     *
     * @return ControllerInterface|self|$this
     */
    abstract public function redirect($path);

    /**
     * Add an error flash message
     *
     * @param string $message
     * @return self
     */
    abstract public function addErrorMessage($message);

    /**
     * Add a success flash message
     *
     * @param string $message
     * @return self
     */
    abstract public function addSuccessMessage($message);

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

    /**
     * Get entity singular name used on controller actions
     *
     * @return string
     */
    abstract protected function getEntityNameSingular();
}