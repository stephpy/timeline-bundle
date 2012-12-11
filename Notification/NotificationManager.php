<?php

namespace Spy\TimelineBundle\Notification;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\TimelineBundle\Notification\Notifier\NotifierInterface;

/**
 * NotificationManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class NotificationManager
{
    /**
     * @var array
     */
    private $notifiers = array();

    /**
     * @param NotifierInterface $notifier
     */
    public function addNotifier(NotifierInterface $notifier)
    {
        $this->notifiers[] = $notifier;
    }

    /**
     * @param ActionInterface    $action  action notified
     * @param string             $context Context notified
     * @param ComponentInterface $subject Subject notified
     */
    public function notify(ActionInterface $action, $context, ComponentInterface $subject)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notify($action, $context, $subject);
        }
    }
}
