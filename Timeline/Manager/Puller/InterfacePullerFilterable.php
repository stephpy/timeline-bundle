<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Filter\InterfaceFilter;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfacePullerFilterable
{
    /**
     * Adding a filter to the list, when puller will get a colletion of results
     * it will apply results on each filters
     *
     * @param InterfaceFilter $filter
     */
    function addFilter(InterfaceFilter $filter);

    /**
     * @param InterfaceFilter $filter
     */
    function removeFilter(InterfaceFilter $filter);

    /**
     * This action will filters each results given in parameters
     * You have to return results
     *
     * @param array $results
     *
     * @return array
     */
    function filter($results);
}
