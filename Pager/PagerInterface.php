<?php

namespace Spy\TimelineBundle\Pager;

/**
 * PagerInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface PagerInterface
{
    /**
     * @param mixed $target  target
     * @param int   $page    page
     * @param int   $limit   limit
     * @param array $options options
     *
     * @return mixed
     */
    public function paginate($target, $page = 1, $limit = 10, $options = array());
}
