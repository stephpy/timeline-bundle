<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spy\Timeline\ResultBuilder\Pager\PagerInterface;

class Pager implements PagerInterface, \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var int
     */
    protected $lastPage;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $nbResults;

    public function paginate($target, $page = 1, $limit = 10)
    {
        if (!$target instanceof DoctrineQueryBuilder) {
            throw new \Exception('Not supported yet');
        }

        if ($limit) {
            $offset = ($page - 1) * (int) $limit;

            $target
                ->setFirstResult($offset)
                ->setMaxResults($limit)
            ;
        }

        $paginator = new Paginator($target, true);
        $this->page = $page;
        $this->items = (array) $paginator->getIterator();
        $this->nbResults = \count($paginator);
        $this->lastPage = (int) ceil($this->nbResults / $limit);

        // Clone is not the best design here but we should not have any state
        // on this pager or at least it should not be injected on services with state.

        return clone $this;
    }

    public function getLastPage()
    {
        return $this->lastPage;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function haveToPaginate()
    {
        return $this->getLastPage() > 1;
    }

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
     * @return int
     */
    public function count()
    {
        return \count($this->items);
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
}
