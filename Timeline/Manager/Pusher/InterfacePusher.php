<?php

namespace Highco\TimelineBundle\Timeline\Manager\Pusher;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * Pusher
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface InterfacePusher
{
    /**
     * push the timeline_action
     *
     * @param TimelineAction $timeline_action
     * @return boolean
     */
    public function push(TimelineAction $timeline_action);
}
