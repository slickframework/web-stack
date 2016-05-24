<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Slick\Mvc\Controller;
use Slick\Mvc\Http\FlashMessagesMethods;

/**
 * CRUD Controller
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class CrudController extends Controller
{

    /**
     * To output error/success messages
     */
    use FlashMessagesMethods;
    
    /**
     * For list and filter handling
     */
    use EntityListingMethods;

    /**
     * For handling entity view request
     */
    use EntityViewMethods;
}