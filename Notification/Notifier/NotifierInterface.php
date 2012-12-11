<?php

namespace Spy\TimelineBundle\Notification\Notifier;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;

/**
 * NotifierInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface NotifierInterface
{
    /**
     * @param  ActionInterface    $action  action notified
     * @param  string             $context context notified
     * @param  ComponentInterface $subject Subject notified
     */
    public function notify(ActionInterface $action, $context, ComponentInterface $subject);
}
