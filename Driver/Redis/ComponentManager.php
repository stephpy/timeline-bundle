<?php

namespace Spy\TimelineBundle\Driver\Redis;

use Spy\TimelineBundle\Driver\ComponentManagerInterface;

class ComponentManager implements ComponentManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchOrCreate($model, $identifier = null)
    {
        exit('not implemented yet.');
    }
}
