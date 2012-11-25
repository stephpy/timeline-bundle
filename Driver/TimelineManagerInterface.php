<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ComponentInterface;
use Spy\TimelineBundle\Model\TimelineInterface;

interface TimelineManagerInterface
{
    /**
     * persist a timeline.
     *
     * @param ActionInterface    $action  action
     * @param ComponentInterface $subject subject
     * @param string             $context context
     * @param mixed              $type    timeline type (timeline, notification)
     */
    public function persist(ActionInterface $action, ComponentInterface $subject, $context = 'GLOBAL', $type = TimelineInterface::TYPE_TIMELINE);

    /**
     * flush persist timelines
     */
    public function flush();
}
