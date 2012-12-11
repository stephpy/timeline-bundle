<?php

namespace Spy\TimelineBundle\Notification\Unread;

use Spy\TimelineBundle\Driver\TimelineManagerInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ComponentInterface;
use Spy\TimelineBundle\Notification\Notifier\NotifierInterface;

/**
 * UnreadNotificationManager
 *
 * @uses NotifierInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class UnreadNotificationManager implements NotifierInterface
{
    /**
     * @var TimelineManager
     */
    private $timelineManager;

    /**
     * @param TimelineManagerInterface $timelineManager timelineManager
     */
    public function __construct(TimelineManagerInterface $timelineManager)
    {
        $this->timelineManager = $timelineManager;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(ActionInterface $action, $context, ComponentInterface $subject)
    {
        $this->timelineManager->createAndPersist($action, $subject, $context, 'notification');
        $this->timelineManager->flush();
    }

    /**
     * @param ComponentInterface $subject The subject
     * @param string             $context The context
     * @param array              $options An array of options (offset, limit), see your timelineManager
     *
     * @return array
     */
    public function getUnreadNotifications(ComponentInterface $subject, $context = "GLOBAL", array $options = array())
    {
        $topions['context'] = $context;
        $options['type']    = 'notification';

        return $this->timelineManager->getTimeline($subject, $options);
    }

    /**
     * count how many timeline had not be read
     *
     * @param ComponentInterface $subject The subject
     * @param string             $context      The context
     *
     * @return integer
     */
    public function countKeys(ComponentInterface $subject, $context = "GLOBAL")
    {
        $options = array(
            'context' => $context,
            'type'    => 'notification',
        );

        return $this->timelineManager->countKeys($subject, $options);
    }

    /**
     * @param ComponentInterface $subject  The subject
     * @param string             $actionId The actionId
     * @param string             $context  The context
     */
    public function markAsReadAction(ComponentInterface $subject, $timelineActionId, $context = "GLOBAL")
    {
        $this->markAsReadActions(array(
            array($context, $subject, $timelineActionId)
        ));
    }

    /**
     * Give an array like this
     * array(
     *   array( *CONTEXT*, *SUBJECT*, *KEY* )
     *   array( *CONTEXT*, *SUBJECT*, *KEY* )
     *   ....
     * )
     *
     * @param array $actions
     */
    public function markAsReadActions(array $actions)
    {
        $options = array(
            'type' => 'notification',
        );

        foreach ($actions as $action) {
            $context  = $timelineAction[0];
            $subject  = $timelineAction[1];
            $actionId = $timelineAction[2];

            $options['context'] = $context;

            $this->timelineManager->remove($subject, $actionId, $options);
        }

        $this->timelineManager->flush();
    }

    /**
     * markAllAsRead
     *
     * @param ComponentInterface $subject subject
     * @param string             $context The context
     */
    public function markAllAsRead(ComponentInterface $subject, $context = "GLOBAL")
    {
        $options = array(
            'context' => $context,
            'type'    => 'notification',
        );

        $this->timelineManager->removeAll($subject, $options);
        $this->timelineManager->flush();
    }
}
