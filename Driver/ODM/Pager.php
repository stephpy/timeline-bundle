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
    /**
     * @var array
     */
    protected $items = array();

    /**
     * @var integer
     */
    protected $lastPage;

    /**
     * @var integer
     */
    protected $nbResults;

    /**
     * {@inheritdoc}
     */
    public function paginate($target, $page = 1, $limit = 10)
    {
        if (!$target instanceof Builder) {
            throw new \Exception('Not supported yet');
        }

        $clone = clone $target;
        if ($limit) {
            $skip = $limit * ($page - 1);

            $target
                ->skip($skip)
                ->limit($limit);
        }

        $this->items     = $target->getQuery()->execute()->toArray();
        $this->page      = $page;
        $this->nbResults = $clone->getQuery()->count();
        $this->lastPage  = intval(ceil($this->nbResults / $limit));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function haveToPaginate()
    {
        return $this->getLastPage() > 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        return $this->nbResults;
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
