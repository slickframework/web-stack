<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\Command\Task;

use Slick\Common\Base;

/**
 * Class CreateController
 *
 * @package Slick\Mvc\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property string $modelName
 * @property string $path
 *
 * @method bool isScaffold()
 */
class CreateController extends Base
{

    /**
     * @readwrite
     * @var string
     */
    protected $modelName;

    /**
     * @readwrite
     * @var string
     */
    protected $path;

    /**
     * @readwrite
     * @var bool
     */
    protected $scaffold = false;

    /**
     * @readwrite
     * @var string
     */
    protected $output = 'Controller';

    /**
     * Create Controller command
     *
     * @param string       $modelName
     * @param array|object $options
     */
    public function __construct($modelName, $options = [])
    {
        $this->modelName = $modelName;
        parent::__construct($options);
    }

}