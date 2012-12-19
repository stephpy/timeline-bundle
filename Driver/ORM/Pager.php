<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spy\Timeline\ResultBuilder\Pager\PagerInterface;
use Spy\Timeline\Filter\FilterManagerInterface;

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
    public function paginate($target, $page = 1, $limit = 10, $options = array())
    {
        if (!$target instanceof DoctrineQueryBuilder) {
            throw new \Exception('Not supported yet');
        }

        if ($limit) {
            $offset = ($page - 1) * (int) $limit;

            $target
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        $paginator       = new Paginator($target, true);
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
