<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\ProviderInterface;
use Highco\TimelineBundle\Timeline\Filter\FilterInterface;

/**
 * Puller with filter methods
 *
 * @abstract
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractPullerFilterable
{
    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param FilterInterface $filter
     */
    public function removeFilter(FilterInterface $filter)
    {
        foreach ($this->filters as $key => $filterExisting) {
            if ($filterExisting == $filter) {
                unset($this->filters[$key]);
            }
        }
    }

    /**
     * This action will filters each results given in parameters
     * You have to return results
     *
     * @param array $results
     *
     * @return array
     */
    public function filter($results)
    {
        $filters = $this->filters;

        foreach ($filters as $filter) {
            $results = $filter->filter($results);
        }

        return $results;
    }
}
