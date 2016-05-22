<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Fixtures\Domain;

use Slick\Orm\Annotations as Orm;
use Slick\Orm\Entity;
use Slick\Orm\EntityInterface;

/**
 * Post entity class for tests
 * 
 * @package Slick\Tests\Mvc\Fixtures\Domain
 * @author  Filipe Silva <silvam.filipe@gmailc.om>
 */
class Post extends Entity
{

    /**
     * @readwrite
     * @Orm\Column type=integer, size=big, primaryKey, autoIncrement
     * @var int
     */
    protected $id;

    /**
     * @readwrite
     * @Orm\Column type=text
     * @var string
     * @display
     */
    protected $name;

    /**
     * Returns entity ID
     *
     * This is usually the primary key or a UUID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets entity ID
     *
     * @param mixed $entityId Primary key or a UUID
     *
     * @return self|$this|EntityInterface
     */
    public function setId($entityId)
    {
        $this->id = $entityId;
        return $this->id;
    }
}