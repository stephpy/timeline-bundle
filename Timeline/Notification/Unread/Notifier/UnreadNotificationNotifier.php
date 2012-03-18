<?php

namespace Highco\TimelineBundle\Timeline\Notification\Unread\Notifier;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Notification\Notifier\NotifierInterface;

/**
 * UnreadNotificationNotifier
 *
 * @uses NotifierInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class UnreadNotificationNotifier implements NotifierInterface
{
	/**
	 * {@inheritedDoc}
	 */
	public function notify(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
	{
		//@todo adding to redis unread notifications
	}
}
