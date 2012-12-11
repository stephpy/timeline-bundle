<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Spy\Timeline\Pager\PagerInterface;
use Spy\Timeline\Filter\FilterManagerInterface;

/**
 * Pager
 *
 * @uses PagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Pager implements PagerInterface
{
    protected $items = array();

    /**
     * @var FilterManagerInterface
     */
    protected $filterManager;

    /**
     * @param FilterManagerInterface $filterManager filterManager
     */
    public function __construct(FilterManagerInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($target, $page = 1, $limit = 10, $options = array())
    {
        if (!$target instanceof QueryBuilder) {
            throw new \Exception('Not supported yet');
        }

        if ($limit) {
            $offset = ($page - 1) * (int) $limit;

            $target
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        $paginator   = new Paginator($target, true);
        $this->items = (array) $paginator->getIterator();

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
}
