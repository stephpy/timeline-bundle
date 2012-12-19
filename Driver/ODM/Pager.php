<?php

namespace Spy\TimelineBundle\Driver\ODM;

use Doctrine\ODM\MongoDB\Query\Builder;
use Spy\Timeline\Filter\FilterManager;
use Spy\Timeline\ResultBuilder\Pager\PagerInterface;

/**
 * Pager
 *
 * @uses PagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Pager implements PagerInterface, \IteratorAggregate, \Countable
{
    protected $items = array();

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @param FilterManager $filterManager filterManager
     */
    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($target, $page = 1, $limit = 10, $options = array())
    {
        if (!$target instanceof Builder) {
            throw new \Exception('Not supported yet');
        }

        if ($limit) {
            $skip = $limit * ($page - 1);

            $target
                ->skip($skip)
                ->limit($limit);
        }

        $this->items = $target->getQuery()
            ->toArray();

        return $this;
    }

    public function filter($pager)
    {
        return $this->filterManager->filter($pager->getItems());
    }

    /**
     * @return rray
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }
}
