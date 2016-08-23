<?php

/**
 * This file is part of Mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\MetaDataGenerator;

use Slick\Mvc\Console\MetaDataGeneratorInterface;

/**
 * Entity meta data for CRUD controller creation
 *
 * @package Slick\Mvc\Console\MetaDataGenerator
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class Entity extends AbstractMetaDataGenerator implements
    MetaDataGeneratorInterface
{

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Entity
     *
     * @param string $entityName
     */
    public function __construct($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * Get generated data array
     *
     * @return array
     */
    public function getData()
    {
        if (empty($this->data)) {
            $this->data = [
                'entityClassName' => $this->getEntityClassName(),
                'entityName' => $this->getEntityName(),
                'formFilename' => lcfirst($this->getEntityName()).'-form'
            ];
        }
        return $this->data;
    }

    /**
     * Returns the FQ entity name
     *
     * @return string
     */
    protected function getEntityClassName()
    {
        return $this->entityName;
    }

    /**
     * Get entity name
     *
     * @return string
     */
    protected function getEntityName()
    {
        $names = explode("\\", $this->entityName);
        return end($names);
    }
}