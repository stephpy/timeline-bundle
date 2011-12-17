<?php

namespace Highco\TimelineBundle\Timeline\Filter\DataHydrator;

/**
 * Reference
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Reference
{
    public $model;
    public $id;

    /**
     * __construct
     *
     * @param string $model
     * @param string $id
     */
    public function __construct($model, $id)
    {
        $this->model = $model;
        $this->id    = $id;
    }

    /**
     * Return an unique key of the reference.
     *
     * @return string
     */
    public function getKey()
    {
        return sprintf('%s:%s', $this->model, $this->id);
    }
}
