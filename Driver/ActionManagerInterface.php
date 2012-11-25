<?php

namespace Spy\TimelineBundle\Driver;

interface ActionManagerInterface
{
    /**
     * Find a component or create it.
     *
     * @param string|object     $model      pass an object and second argument will be ignored.
     * it'll be replaced by $model->getId();
     * @param null|string|array $identifier pass an array for composite keys.
     *
     * @return Component
     */
    public function findOrCreateComponent($model, $identifier = null);
}
