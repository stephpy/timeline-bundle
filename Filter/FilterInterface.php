<?php

namespace Spy\TimelineBundle\Filter;

/**
 * This interface define how filters must be used
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface FilterInterface
{
    /**
     * @return integer
     */
    public function getPriority();

    /**
     * This action will filters each results given in parameters
     * You have to return results
     *
     * @param array|\Traversable $collection
     *
     * @return array
     */
    public function filter($collection);
}
