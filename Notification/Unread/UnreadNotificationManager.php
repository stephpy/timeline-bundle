<?php

namespace Highco\TimelineBundle\Notification\Unread;

use Highco\TimelineBundle\Notification\Notifier\NotifierInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Provider\ProviderInterface;

/**
 * UnreadNotificationManager
 *
 * @uses NotifierInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class UnreadNotificationManager implements NotifierInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var string
     */
    private static $unreadNotificationKey = "TimelineUnreadNotification:%s:%s:%s";

    /**
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function notify(TimelineAction $timelineAction, $context, $subjectModel, $subjectId)
    {
        $options = array(
            'key' => self::$unreadNotificationKey,
        );

        $this->provider->persist($timelineAction, $context, $subjectModel, $subjectId, $options);
        $this->provider->flush();
    }

    /**
     * getTimelineActions
     *
     * If you want to apply filters to these results,
     * $actions = $this->get('highco.timeline.manager')->applyFilters($actions);
     *
     * @param string $subjectModel The class of subject
     * @param string $subjectId    The oid of subject
     * @param string $context      The context
     * @param array  $options      An array of options (offset, limit), see your provider
     *
     * @return array
     */
    public function getTimelineActions($subjectModel, $subjectId, $context = "GLOBAL", array $options = array())
    {
        $params = array(
            'subjectModel' => $subjectModel,
            'subjectId'    => $subjectId,
            'context'      => $context,
        );

        $options['key'] = self::$unreadNotificationKey;

        return $this->provider->getWall($params, $options);
    }

    /**
     * count how many timeline had not be read
     *
     * @param string $subjectModel The class of subject
     * @param string $subjectId    The oid of subject
     * @param string $context      The context
     *
     * @return integer
     */
    public function countKeys($subjectModel, $subjectId, $context = "GLOBAL")
    {
        $options = array(
            'key' => self::$unreadNotificationKey,
        );

        return $this->provider->countKeys($context, $subjectModel, $subjectId, $options);
    }

    /**
     * @param string $subjectModel     The class of subject
     * @param string $subjectId        The oid of subject
     * @param string $timelineActionId The timelineActionId
     * @param string $context          The context
     */
    public function markAsReadTimelineAction($subjectModel, $subjectId, $timelineActionId, $context = "GLOBAL")
    {
        $this->markAsReadTimelineActions(array(
            array($context, $subjectModel, $subjectId, $timelineActionId)
        ));
    }

    /**
     * Give an array like this
     * array(
     *   array( *CONTEXT*, *SUBJECT_MODEL*, *SUBJECT_ID*, *KEY* )
     *   array( *CONTEXT*, *SUBJECT_MODEL*, *SUBJECT_ID*, *KEY* )
     *   ....
     * )
     *
     * @param array $timelineActions
     */
    public function markAsReadTimelineActions(array $timelineActions)
    {
        $options = array(
            'key' => self::$unreadNotificationKey,
        );

        foreach ($timelineActions as $timelineAction) {
            $context          = $timelineAction[0];
            $subjectModel     = $timelineAction[1];
            $subjectId        = $timelineAction[2];
            $timelineActionId = $timelineAction[3];

            $this->provider->remove($context, $subjectModel, $subjectId, $timelineActionId, $options);
        }

        $this->provider->flush();
    }

    /**
     * markAllAsRead
     *
     * @param string $subjectModel The class of subject
     * @param string $subjectId    The oid of subject
     * @param string $context      The context
     */
    public function markAllAsRead($subjectModel, $subjectId, $context = "GLOBAL")
    {
        $options = array(
            'key' => self::$unreadNotificationKey,
        );

        $this->provider->removeAll($context, $subjectModel, $subjectId, $options);
        $this->provider->flush();
    }
}
