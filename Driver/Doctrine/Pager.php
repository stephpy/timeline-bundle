<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Spy\TimelineBundle\Pager\PagerInterface;
use Doctrine\ORM\QueryBuilder;
use Spy\TimelineBundle\Filter\FilterManager;

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
        if (!$target instanceof QueryBuilder) {
            throw new \Exception('Not supported yet');
        }

        $target->setFirstResult(($page - 1) * (int) $limit);

        if ($limit) {
            $target->setMaxResults($limit);
        }

        $this->items = $target->getQuery()
            ->getResult();

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
