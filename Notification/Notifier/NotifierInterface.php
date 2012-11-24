<?php

namespace Spy\TimelineBundle\Notification\Notifier;

use Spy\TimelineBundle\Model\TimelineAction;

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
     * @param  TimelineAction $timelineAction timeline action to notify
     * @param  string         $context        Context where we want to notify
     * @param  string         $subjectModel   Subject model where we have to notify
     * @param  string         $subjectId      Subject id where we have to notify
     * @return void
     */
    public function notify(TimelineAction $timelineAction, $context, $subjectModel, $subjectId);
}
