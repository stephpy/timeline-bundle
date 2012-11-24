<?php

namespace Spy\TimelineBundle\Filter;

/**
 * AbstractFilter
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractFilter
{
    protected $options;

    /**
     * @param array $options options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $key     key
     * @param mixed  $default default
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ?  $this->options[$key]:  $default;
    }
}
