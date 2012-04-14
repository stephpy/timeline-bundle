<?php

namespace Highco\TimelineBundle\Filter\DataHydrator;

/**
 * This class represent a reference for an entry,
 * properties "model, and id" become an object
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Reference
{
    /**
     * @var string
     */
    public $model;

    /**
     * @var object
     */
    public $object;

    /**
     * @var string
     */
    public $id;

    /**
     * @param string $model The model of reference
     * @param string $id    The id of model
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
