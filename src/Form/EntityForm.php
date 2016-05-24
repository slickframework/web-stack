<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Form;

use Slick\Form\Form;
use Slick\Form\FormInterface;

/**
 * Entity Form
 * 
 * @package Slick\Mvc\Form
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityForm extends Form implements FormInterface
{

    /**
     * Returns submitted or current data
     *
     * This method overrides the default behavior to unset the form-id from
     * submitted data if it exists.
     * 
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if (array_key_exists('form-id', $data)) {
            unset($data['form-id']);
        }
        return $data;
    }
}