<?php

namespace Spy\TimelineBundle\Driver;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;

interface TimelineManagerInterface
{
    /**
     * @param ComponentInterface $subject subject
     * @param array              $options options
     */
    public function getTimeline(ComponentInterface $subject, array $options = array());

    /**
     * count how many keys are stored
     *
     * @param ComponentInterface $subject subject
     * @param array              $options options
     *
     * @return integer
     */
    public function countKeys(ComponentInterface $subject, array $options = array());

    /**
     * remove key from storage
     * This action has to be flushed
     *
     * @param  ComponentInterface $subject  subject
     * @param  string             $actionId The action id
     * @param  array              $options  Array of options
     * @return void
     */
    public function remove(ComponentInterface $subject, $actionId, array $options = array());

    /**
     * remove all keys from storage
     * This action has to be flushed
     *
     * @param  ComponentInterface $subject  subject
     * @param  array  $options      Array of options
     * @return void
     */
    public function removeAll(ComponentInterface $subject, array $options = array());

    /**
     * create and persist a timeline.
     *
     * @param ActionInterface    $action  action
     * @param ComponentInterface $subject subject
     * @param string             $context context
     * @param mixed              $type    timeline type (timeline, notification)
     */
    public function createAndPersist(ActionInterface $action, ComponentInterface $subject, $context = 'GLOBAL', $type = TimelineInterface::TYPE_TIMELINE);

    /**
     * flush persist timelines
     */
    public function flush();
}
