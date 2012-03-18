<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

/**
 * How to make a puller
 *
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
interface PullerInterface
{
    /**
     * Pull results
     *
     * @param string $type    Type of timeline to retrieve
     * @param array  $params  parameters to give to the provider
     * @param array  $options optional
     *
     * @return array or Exception
     */
    function pull($type, $params, $options = array());
}
