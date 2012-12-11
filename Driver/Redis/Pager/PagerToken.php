<?php

namespace Spy\TimelineBundle\Driver\Redis\Pager;

/**
 * PagerToken
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class PagerToken
{
    public $key;
    public $page;
    public $maxPerPage;

    /**
     * @param string $key        key
     */
    public function __construct($key)
    {
        $this->key        = $key;
    }
}
