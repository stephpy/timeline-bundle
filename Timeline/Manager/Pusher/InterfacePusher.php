<?php

namespace Highco\TimelineBundle\Timeline\Manager\Pusher;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfacePusher
{
    /**
     * @param TimelineAction $timelineAction
     *
     * @return boolean
     */
    function push(TimelineAction $timelineAction);
}
