<?php

namespace Spy\TimelineBundle\Pager;

use Knp\Component\Pager\Paginator;

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
     * @param Paginator $paginator paginator
     */
    public function __construct(Paginator $paginator = null)
    {
        if (null === $paginator) {
            throw new \LogicException('Please install KnpPagerBundle or disable paginator');

        }

        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($target, $page = 1, $limit = 10, $options = array())
    {
        return $this->paginator->paginate($target, $page, $limit, $options);
    }
}
