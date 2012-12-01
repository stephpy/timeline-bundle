<?php

namespace Spy\TimelineBundle\Filter;

use Spy\TimelineBundle\Model\Collection;

/**
 * This interface define how filters must be used
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface FilterInterface
{
    /**
     * @param integer $priority priority
     */
    public function setPriority($priority);

    /**
     * @return integer
     */
    public function getPriority();

    /**
     * This action will filters each results given in parameters
     * You have to return results
     *
     * @param Collection $collection
     *
     * @return array
     */
    public function filter(Collection $collection);
}
