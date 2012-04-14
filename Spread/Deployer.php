<?php

namespace Highco\TimelineBundle\Spread;

use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Provider\ProviderInterface;
use Highco\TimelineBundle\Notification\NotificationManager;
use Highco\TimelineBundle\Model\TimelineActionManagerInterface;

/**
 * Deployer class, this class will deploy on spread
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Deployer
{
    CONST DELIVERY_IMMEDIATE = 'immediate';
    CONST DELIVERY_WAIT      = 'wait';

    /**
     * @var string
     */
    private $delivery = 'immediate';

    /**
     * @var Manager
     */
    private $spreadManager;

    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var TimelineActionManagerInterface
     */
    private $timelineActionManager;

    /**
     * @var NotificationManager
     */
    private $notificationManager;

    /**
     * @param Manager                        $spreadManager         Spread manager to retrieve entries where to deploy
     * @param TimelineActionManagerInterface $timelineActionManager ObjectManager to notify Action is published
     * @param ProviderInterface              $provider              Provider to deploy
     * @param NotificationManager            $notificationManager   Notificaiton manager
     */
    public function __construct(Manager $spreadManager, TimelineActionManagerInterface $timelineActionManager, ProviderInterface $provider, NotificationManager $notificationManager)
    {
        $this->spreadManager         = $spreadManager;
        $this->timelineActionManager = $timelineActionManager;
        $this->provider              = $provider;
        $this->notificationManager   = $notificationManager;
    }

    /**
     * @param TimelineAction $timelineAction
     */
    public function deploy(TimelineAction $timelineAction)
    {
        if ($timelineAction->getStatusWanted() !== TimelineAction::STATUS_PUBLISHED) {
            return;
        }

        $this->spreadManager->process($timelineAction);
        $results = $this->spreadManager->getResults();

        foreach ($results as $context => $values) {
            foreach ($values as $entry) {
                $this->provider->persist($timelineAction, $context, $entry->subjectModel, $entry->subjectId);
                $this->notificationManager->notify($timelineAction, $context, $entry->subjectModel, $entry->subjectId);
            }
        }

        $this->provider->flush();

        $timelineAction->setStatusCurrent(TimelineAction::STATUS_PUBLISHED);
        $timelineAction->setStatusWanted(TimelineAction::STATUS_FROZEN);

        $this->timelineActionManager->updateTimelineAction($timelineAction);

        $this->spreadManager->clear();
    }

    /**
     * @param string $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return string
     */
    public function getDelivery()
    {
        return $this->delivery;
    }
}
