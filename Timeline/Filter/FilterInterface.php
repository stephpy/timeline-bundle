<?php

namespace Highco\TimelineBundle\Timeline\Filter;

/**
 * This interface define how filters must be used
 *
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
interface FilterInterface
{
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
