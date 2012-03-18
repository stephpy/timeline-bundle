<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

use Highco\TimelineBundle\Timeline\Filter\FilterInterface;

/**
 * Define methods to make pull filterfable
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface PullerFilterableInterface
{
    /**
     * Adding a filter to the list, when puller will get a colletion of results
     * it will apply results on each filters
     *
     * @param FilterInterface $filter
     */
    function addFilter(FilterInterface $filter);

    /**
     * @param FilterInterface $filter
     */
    function removeFilter(FilterInterface $filter);

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
