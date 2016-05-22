<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service\Entity\QueryFilter;

use Slick\Common\Base;
use Slick\Mvc\Service\Entity\QueryFilterInterface;
use Slick\Orm\Repository\QueryObject\QueryObjectInterface;

/**
 * Search Filter
 * 
 * @package Slick\Mvc\Service\Entity\QueryFilter
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 * 
 * @property string   $pattern
 * @property string[] $fields
 */
class SearchFilter extends Base implements QueryFilterInterface
{

    /**
     * @readwrite
     * @var string
     */
    protected $pattern;

    /**
     * @readwrite
     * @var string[]
     */
    protected $fields = [];
    
    /**
     * Applies the filter to the provided query
     *
     * @param QueryObjectInterface $query
     *
     * @return $this|self|QueryFilterInterface
     */
    public function apply(QueryObjectInterface $query)
    {
        if (empty($this->fields)) {
            return $this;
        }
        
        $query->where(
            [
                $this->getQueryCondition() => [
                    ':pattern' => "%{$this->pattern}%"
                ]
            ]
        );
        return $this;
    }

    /**
     * Gets the query condition for provided fields
     * 
     * @return string
     */
    protected function getQueryCondition()
    {
        $parts = [];
        foreach ($this->fields as $field) {
            $parts[] = "{$field} LIKE :pattern";
        }
        return implode(' AND ', $parts);
    }
}