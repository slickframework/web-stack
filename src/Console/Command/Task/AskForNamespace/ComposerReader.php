<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task\AskForNamespace;

/**
 * ComposerReader
 *
 * @package Slick\WebStack\Console\Command\Task\AskForNamespace
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ComposerReader
{
    /**
     * @var NameSpaceCollection
     */
    private $collection;

    /**
     * @var object
     */
    private $data;

    /**
     * @var string
     */
    private $basePath;

    /**
     * Creates a composer reader
     *
     * @param string $composerFile
     */
    public function __construct($composerFile)
    {
        $this->read($composerFile);
        $this->collection = new NameSpaceCollection();
    }

    /**
     * Get the namespaces collection
     *
     * @return NameSpaceCollection|NameSpaceEntry[]
     */
    public function nameSpaces()
    {
        foreach ($this->data['autoload'] as $item) {
            $this->add($item);
        }
        foreach ($this->data['autoload-dev'] as $item) {
            $this->add($item);
        }
        return $this->collection;
    }

    /**
     * Read composer file
     *
     * @param string $composerFile
     */
    private function read($composerFile)
    {
        $this->basePath = dirname($composerFile);
        $this->data = json_decode(file_get_contents($composerFile), true);
    }

    /**
     * Adds a new item to the collection
     *
     * @param array $item
     */
    private function add(array $item)
    {
        foreach ($item as $nameSpace => $path) {
            $entry = new NameSpaceEntry($nameSpace, $this->composePath($path));
            $this->collection->offsetSet(null, $entry);
        }
    }

    /**
     * Set the base path to the namespace path
     *
     * @param string $path
     *
     * @return string
     */
    private function composePath($path)
    {
        return $this->basePath .'/'. $path;
    }
}