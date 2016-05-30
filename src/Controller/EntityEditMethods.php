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
use Slick\Orm\EntityInterface;

/**
 * Entity Edit Methods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <Silvam.filipe@gmail.com>
 */
trait EntityEditMethods
{

    /**
     * Handle the request to edit an entity
     * 
     * @param mixed $entityId
     */
    public function edit($entityId)
    {
        $entity = $this->show($entityId);
        $form = $this->getForm();
        $this->set(compact('form'));
        
        if (!$entity instanceof EntityInterface) {
            return;
        }

        $form->setData($entity->asArray());
        
        if (!$form->wasSubmitted()) {
            return;
        }

        try {
            $this->getUpdateService()
                ->setEntity($entity)
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
            $this->getEditSuccessMessage($this->getUpdateService()->getEntity())
        );
        $this->redirectFromEdit($entity);
    }

    /**
     * Redirect after successful entity change
     *
     * @param EntityInterface $entity
     *
     * @return $this|ControllerInterface|static
     */
    protected function redirectFromEdit(EntityInterface $entity)
    {
        return $this->redirect(
            $this->getBasePath().'/show/'.$entity->getId()
        );
    }

    /**
     * Get the update successful entity message
     *
     * @param EntityInterface $entity
     *
     * @return string
     */
    protected function getEditSuccessMessage(EntityInterface $entity)
    {
        $singleName = $this->getEntityNameSingular();
        $message = "The {$singleName} '%s' was successfully updated.";
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
     * Handles the request to view an entity
     *
     * @param int $entityId
     *
     * @return null|EntityInterface
     */
    abstract public function show($entityId = 0);

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