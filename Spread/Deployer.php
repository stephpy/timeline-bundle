<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Driver\TimelineManagerInterface;
use Spy\TimelineBundle\Model\TimelineInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Notification\NotificationManager;

/**
 * Deployer
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Deployer
{
    CONST DELIVERY_IMMEDIATE = 'immediate';
    CONST DELIVERY_WAIT      = 'wait';

    /**
     * @var SpreadManager
     */
    protected $spreadManager;

    /**
     * @var NotificationManager
     */
    protected $notificationManager;

    /**
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var TimelineManagerInterface
     */
    protected $timelineManager;

    /**
     * @var string
     */
    protected $delivery;

    /**
     * @param SpreadManager            $spreadManager       spreadManager
     * @param NotificationManager      $notificationManager notificationManager
     * @param ActionManagerInterface   $actionManager       actionManager
     * @param TimelineManagerInterface $timelineManager     timelineManager
     */
    public function __construct(SpreadManager $spreadManager, NotificationManager $notificationManager, ActionManagerInterface $actionManager, TimelineManagerInterface $timelineManager)
    {
        $this->spreadManager       = $spreadManager;
        $this->notificationManager = $notificationManager;
        $this->actionManager       = $actionManager;
        $this->timelineManager     = $timelineManager;
    }

    /**
     * @param ActionInterface $action action
     */
    public function deploy(ActionInterface $action)
    {
        if (!$action->getId()) {
            $this->actionManager->updateAction($action);
        }

        if ($action->getStatusWanted() !== ActionInterface::STATUS_PUBLISHED) {
            return;
        }

        $results = $this->spreadManager->process($action);
        $results->loadUnawareEntries();

        foreach ($results as $context => $entries) {
            foreach ($entries as $entry) {
                $this->timelineManager->createAndPersist($action, $entry->getSubject(), $context, TimelineInterface::TYPE_TIMELINE);
                $this->notificationManager->notify($action, $context, $entry->getSubject());
            }
        }

        if (count($results)) {
            $this->timelineManager->flush();
        }

        $action->setStatusCurrent(ActionInterface::STATUS_PUBLISHED);
        $action->setStatusWanted(ActionInterface::STATUS_FROZEN);

        $this->actionManager->updateAction($action);

        $this->spreadManager->clear();
    }

    /**
     * @param string $delivery delivery
     */
    public function setDelivery($delivery)
    {
        $availableDelivery = array(self::DELIVERY_IMMEDIATE, self::DELIVERY_WAIT);

        if (!in_array($delivery, $availableDelivery)) {
            throw new \InvalidArgumentException(sprintf('Delivery "%s" is not supported, (%s)', $delivery, implode(', ', $availableDelivery)));
        }

        $this->delivery = $delivery;
    }
}
