<?php

namespace Highco\TimelineBundle\Pager\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Highco\TimelineBundle\Pager\TimelinePagerToken;
use Highco\TimelineBundle\Notification\Unread\UnreadNotificationManager;
use Highco\TimelineBundle\Manager;

/**
 * TimelineSubscriber
 *
 * @uses EventSubscriberInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineSubscriber implements EventSubscriberInterface
{
    private $manager;
    private $unreadNotifications;

    /**
     * @param Manager                   $manager             manager
     * @param UnreadNotificationManager $unreadNotifications unreadNotifications
     */
    public function __construct(Manager $manager, UnreadNotificationManager $unreadNotifications)
    {
        $this->manager             = $manager;
        $this->unreadNotifications = $unreadNotifications;
    }

    /**
     * @param ItemsEvent $event event
     */
    public function items(ItemsEvent $event)
    {
        if ($event->target instanceof TimelinePagerToken) {
            $token        = $event->target;
            $subjectClass = $token->subjectClass;
            $subjectId    = $token->subjectId;
            $context      = $token->context;

            $options = array_merge($token->options, array(
                'offset' => $event->getOffset(),
                'limit'  => $event->getLimit(),
            ));

            if ($token->getService() == TimelinePagerToken::SERVICE_TIMELINE) {
                $event->count = $this->manager->countWallEntries($subjectClass, $subjectId, $context);
                $event->items = $this->manager->getWall($subjectClass, $subjectId, $context, $options);
            } elseif ($token->getService() == TimelinePagerToken::SERVICE_NOTIFICATION) {
                $event->count = $this->unreadNotifications->countKeys($subjectClass, $subjectId, $context);
                $event->items = $this->unreadNotifications->getTimelineActions($subjectClass, $subjectId, $context, $options);
            } elseif ($token->getService() == TimelinePagerToken::SERVICE_SUBJECT_TIMELINE) {
                $event->count = $this->manager->countTimeline($subjectClass, $subjectId, $options);
                $event->items = $this->manager->getTimeline($subjectClass, $subjectId, $options);
            }
            $event->stopPropagation();
        }
    }

    /**
     * @return array<string,array<string|integer>>
     */
    public static function getSubscribedEvents()
    {
        return array(
            'knp_pager.items' => array('items', 1)
        );
    }

}
