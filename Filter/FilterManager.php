<?php

namespace Spy\TimelineBundle\Filter;

use Spy\TimelineBundle\Model\Collection;

/**
 * FilterManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class FilterManager
{
    /**
     * @var array<FilterInterface>
     */
    protected $filters = array();

    /**
     * @var boolean
     */
    protected $sorted = true;

    /**
     * @param FilterInterface $filter filter
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        $this->sorted    = false;
    }

    /**
     * @param array|Collection $collection collection
     *
     * @return array
     */
    public function filter($collection)
    {
        if (!$this->sorted) {
            $this->sortFilters();
        }

        if (!$collection instanceof Collection) {
            $collection = new Collection($collection);
        }

        foreach ($this->filters as $filter) {
            $collection = $filter->filter($collection);
        }

        return $collection;
    }

    protected function sortFilters()
    {
        usort($this->filters, function($a, $b) {
            $a = $a->getPriority();
            $b = $b->getPriority();

            if ($a == $b) {
                return 0;
            }

            return $a < $b? -1 : 1;
        });

        $this->sorted = true;
    }
}
