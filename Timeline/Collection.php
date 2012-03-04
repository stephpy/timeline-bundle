<?php

namespace Highco\TimelineBundle\Timeline;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1(at)gmail.com>
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array
     */
    protected $coll = array();

    /**
     * This variable is useful coz filter may modify length of the collection.
     *
     * @var int
     */
    protected $initial_count = 0;

    /**
     * @param array $coll
     */
    public function __construct(array $coll = array())
    {
        if (!empty($coll)) {
            $this->setColl($coll);
        }
    }

    /**
     * This method will use offsetSet method to hydrate collection,
     * to be sure each object are instance of timeline action.
     *
     * @param array $coll
     */
    public function setColl(array $coll)
    {
        foreach ($coll as $key => $value) {
            $this->offsetSet($key, $value);
        }

        //this variable is useful coz filter may modify length of the collection
        $this->initial_count = count($this->coll);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->coll);
    }

    /**
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->coll[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->coll[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof TimelineAction) {
            throw new \InvalidArgumentException('Items must extends TimelineAction');
        }

        $this->coll[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->coll[$offset]);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->coll);
    }

    /**
     * @return integer
     */
    public function getInitialCount()
    {
        return $this->initial_count;
    }
}
