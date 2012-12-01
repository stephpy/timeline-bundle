<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Filter\FilterManager;
use Spy\TimelineBundle\Model\Collection;

/**
 * AbstractTimelineManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class AbstractTimelineManager
{
    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @param FilterManager $filterManager filterManager
     */
    public function setFilterManager(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @param array $collection collection
     *
     * @return Collection
     */
    public function filterCollection($collection)
    {
        if ($this->filterManager) {
            return $this->filterManager->filter($collection);
        }

        return $collection;
    }
}
