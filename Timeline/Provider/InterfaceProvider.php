<?php

namespace Highco\TimelineBundle\Timeline\Provider;

use Highco\TimelineBundle\Model\TimelineAction;

interface InterfaceProvider
{
	public function add(TimelineAction $timeline_action, $context, $subject_model, $subject_id);
}
