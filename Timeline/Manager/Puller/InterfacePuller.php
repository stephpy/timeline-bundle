<?php

namespace Highco\TimelineBundle\Timeline\Manager\Puller;

/**
 * Puller
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfacePuller
{
    /**
     * pull
     *
     * @param mixed $type
     * @param mixed $params
     * @param array $options
     * @access public
     * @return void
     */
    public function pull($type, $params, $options = array());
}
