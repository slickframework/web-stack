<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task\AskForNamespace;

/**
 * NameSpaceEntry
 *
 * @package Slick\WebStack\Console\Command\Task\AskForNamespace
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class NameSpaceEntry
{

    /**
     * @var string
     */
    private $nameSpace;

    /**
     * @var string
     */
    private $path;
    /**
     * @var null
     */
    private $basePath;

    /**
     * Creates a Namespace Entry
     *
     * @param string $nameSpace
     * @param string $path
     * @param string $basePath
     */
    public function __construct($nameSpace, $path, $basePath = null)
    {
        $this->nameSpace = $nameSpace;
        $this->path = $path;
        $this->basePath = $basePath;
    }

    /**
     * Get the entry name space
     *
     * @return string
     */
    public function getNameSpace()
    {
        return $this->nameSpace;
    }

    /**
     * Get the path for this name space entry
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the base path for the name spaced classes
     *
     * @return null|string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
}
