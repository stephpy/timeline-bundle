<?php

namespace Highco\TimelineBundle\Timeline\Provider;

/**
 * How to define an entity retriever
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface EntityRetrieverInterface
{
    /**
     * An array of id is given an return with an array of entity/models/doc
     *
     * @param array $ids
     *
     * @return array
     */
    function find(array $ids);
}
