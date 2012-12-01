<?php

namespace Spy\TimelineBundle\Model;

/**
 * Collection
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array
     */
    protected $actions = array();

    /**
     * Theses datas could be used by filters.
     *
     * @var array
     */
    protected $datas = array();

    /**
     * @param array<ActionInterface> $actions
     */
    public function __construct(array $actions = array())
    {
        foreach ($actions as $key => $action) {
            // to validate ActionInterface
            $this->offsetSet($key, $action);
        }
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->actions);
    }

    /**
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->actions[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->actions[$offset];
    }

    /**
     * @param mixed $offset key
     * @param mixed $value  value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof ActionInterface) {
            throw new \InvalidArgumentException('Items must extends ActionInterface');
        }

        $this->actions[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->actions[$offset]);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->actions);
    }

    /**
     * @param string $key   key
     * @param mixed  $value value
     */
    public function setData($key, $value)
    {
        $this->datas[$key] = $value;
    }

    /**
     * @param string $key     key
     * @param mixed  $default default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        return isset($this->datas[$key]) ? $this->datas[$key] : $default;
    }
}
