<?php

namespace Spy\TimelineBundle\Pager;

use Knp\Component\Pager\Paginator;
use Spy\TimelineBundle\Filter\FilterManager;

/**
 * KnpPager
 *
 * @uses PagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class KnpPager implements PagerInterface
{
    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @param Paginator $paginator paginator
     */
    public function __construct(Paginator $paginator = null, FilterManager $filterManager)
    {
        if (null === $paginator) {
            throw new \LogicException('Please install KnpPagerBundle or disable paginator');

        }

        $this->paginator     = $paginator;
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($target, $page = 1, $limit = 10, $options = array())
    {
        return $this->paginator->paginate($target, $page, $limit, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($pager)
    {
        return $this->filterManager->filter($pager);
    }
}
