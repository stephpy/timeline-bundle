<?php

namespace Highco\TimelineBundle\Timeline\Manager\Pusher;

use Highco\TimelineBundle\Model\TimelineAction;

interface InterfacePusher
{
	public function push(TimelineAction $timeline_action);
}
