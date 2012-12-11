<?php

namespace Spy\TimelineBundle\Driver\Redis\Pager;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Spy\TimelineBundle\Driver\ActionManagerInterface;

/**
 * KnpSubscriber
 *
 * @uses EventSubscriberInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class KnpSubscriber implements EventSubscriberInterface
{
    /**
     * @var PredisClient|PhpredisClient
     */
    protected $client;

    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @param PredisClient|PhpredisClient $client        client
     * @param ActionManagerInterface      $actionManager actionManager
     */
    public function __construct($client, ActionManagerInterface $actionManager)
    {
        $this->client        = $client;
        $this->actionManager = $actionManager;
    }

    /**
     * @param ItemsEvent $event event
     */
    public function items(ItemsEvent $event)
    {
        if (!$event->target instanceof PagerToken) {
            return;
        }

        $target = $event->target;
        $offset = $event->getOffset();
        $limit  = $event->getLimit() - 1;

        $ids    = $this->client->zRevRange($target->key, $offset, ($offset + $limit));

        $event->count = $this->client->zCard($target->key);
        $event->items = $this->actionManager->findActionsForIds($ids);
        $event->stopPropagation();
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
