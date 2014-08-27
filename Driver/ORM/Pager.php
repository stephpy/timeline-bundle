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
    protected $items = array();

    /**
     * @var integer
     */
    protected $lastPage;

    /**
     * @var integer
     */
    protected $page;

    /**
     * @var integer
     */
    protected $nbResults;

    /**
     * {@inheritdoc}
     */
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

        $paginator       = new Paginator($target, true);
        $this->page      = $page;
        $this->items     = (array) $paginator->getIterator();
        $this->nbResults = count($paginator);
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

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @param  mixed   $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
}
