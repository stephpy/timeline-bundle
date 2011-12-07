<?php

namespace Highco\TimelineBundle\Timeline\Filter\DataHydrator;

class Reference
{
    public $model;
    public $id;

    public function __construct($model, $id)
    {
        $this->model = $model;
        $this->id    = $id;
    }

    public function getKey()
    {
        return sprintf('%s:%s', $this->model, $this->id);
    }
}
