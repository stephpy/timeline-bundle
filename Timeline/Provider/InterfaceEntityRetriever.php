<?php

namespace Highco\TimelineBundle\Timeline\Provider;

/**
 * EntityRetriever
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @authort stephane py <py.stephane1(at)gmail.com>
 */
interface InterfaceEntityRetriever
{
    /**
     * find
     *
     * An array of id is given an return with an array of entity/models/doc
     *
     * @param array $ids
     * @return array
     */
    public function find(array $ids);
}
