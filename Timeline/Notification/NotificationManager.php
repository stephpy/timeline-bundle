<?php

namespace Highco\TimelineBundle\Timeline\Notification;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Notification\Notifier\NotifierInterface;

/**
 * NotificationManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class NotificationManager
{
    private $notifiers = array();

    /**
     * @param NotifierInterface $notifier
     */
    public function addNotifier(NotifierInterface $notifier)
    {
        $this->notifiers[] = $notifier;
    }

    /**
     * @param TimelineAction $timelineAction timeline action to notify
     * @param string         $context        Context where we want to notify
     * @param string         $subjectModel   Subject model where we have to notify
     * @param string         $subjectId      Subject id where we have to notify
     */
    public function notify(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($timelineAction, $context, $subjectModel, $subjectId);
        }
    }
}
