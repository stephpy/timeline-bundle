<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

use Highco\TimelineBundle\Timeline\Filter\InterfaceFilter;

/**
 * AbstractPuller
 *
 * @abstract
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractPullerFilterable
{
    protected $filters = array();

    /**
     * addFilter
     *
     * @param InterfaceFilter $filter
     */
    public function addFilter(InterfaceFilter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * removeFilter
     *
     * @param InterfaceFilter $filter
     * @access public
     * @return void
     */
    public function removeFilter(InterfaceFilter $filter)
    {
        foreach($this->filters as $key => $filterExisting)
        {
            if($filterExisting == $filter)
            {
                unset($this->filters[$key]);
            }
        }
    }

    /**
     * filter
     *
     * This action will filters each results given in parameters
     * You have to return results
     *
     * @param array $results
     * @return array
     */
    public function filter($results)
    {
        $filters = $this->filters;

        foreach($filters as $filter)
        {
            $results = $filter->filter($results);
        }

        return $results;
    }
}
