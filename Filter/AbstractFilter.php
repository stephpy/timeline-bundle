<?php

namespace Spy\TimelineBundle\Filter;

/**
 * AbstractFilter
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractFilter
{
    /**
     * @var integer
     */
    protected $priority;

    /**
     * @param array $options options
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
