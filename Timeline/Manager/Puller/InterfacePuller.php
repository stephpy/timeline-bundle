<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfacePuller
{
    /**
     * Pull results
     *
     * @param string $type
     * @param array  $params
     * @param array  $options
     *
     * @return array or Exception
     */
    function pull($type, $params, $options = array());
}
