<?php

namespace Spy\TimelineBundle\Tests\Fixtures;

use Spy\TimelineBundle\Filter\FilterInterface;

/**
 * Filter
 *
 * @uses FilterInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Filter implements FilterInterface
{
    /**
     * @param array $results
     *
     * @return void
     */
    public function filter($results)
    {
    }

    /**
     * @param array $options options
     */
    public function initialize(array $options = array())
    {
    }
}
