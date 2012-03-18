<?php

namespace Highco\TimelineBundle\Timeline\Notification\Notifier;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * NotifierInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface NotifierInterface
{
	/**
	 * notify
	 *
	 * @param TimelineAction $timelineAction
	 * @param string         $context
	 * @param string         $subjectModel
	 * @param string         $subjectId
	 */
	public function notify(TimelineAction $timelineAction, $context, $subjectModel, $subjectId);
}
