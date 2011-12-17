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
	public function push(TimelineAction $timeline_action);
}
