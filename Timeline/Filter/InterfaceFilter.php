<?php

namespace Highco\TimelineBundle\Timeline\Filter;

/**
 * Filter
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfaceFilter
{
    /**
     * filter
     *
     * This action will filters each results given in parameters
     * You have to return results
     *
     * @param array $results
     * @return array
     */
    public function filter($results);
}
