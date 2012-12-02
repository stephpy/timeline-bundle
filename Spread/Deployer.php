<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Driver\TimelineManagerInterface;
use Spy\TimelineBundle\Model\TimelineInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Notification\NotificationManager;
use Spy\TimelineBundle\Spread\Entry\Entry;
use Spy\TimelineBundle\Spread\Entry\EntryCollection;

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
     * @var \ArrayIterator
     */
    protected $spreads;

    /**
     * @var EntryCollection
     */
    protected $entryCollection;

    /**
     * @var boolean
     */
    protected $onSubject;

    /**
     * @var NotificationManager
     */
    protected $notificationManager;

    /**
     * @var TimelineManagerInterface
     */
    protected $timelineManager;

    /**
     * @param NotificationManager      $notificationManager notificationManager
     * @param TimelineManagerInterface $timelineManager     timelineManager
     * @param EntryCollection          $entryCollection     entryCollection
     * @param boolean                  $onSubject           onSubject
     */
    public function __construct(NotificationManager $notificationManager, TimelineManagerInterface $timelineManager, EntryCollection $entryCollection, $onSubject = true)
    {
        $this->notificationManager = $notificationManager;
        $this->timelineManager     = $timelineManager;
        $this->entryCollection     = $entryCollection;
        $this->spreads             = new \ArrayIterator();
        $this->onSubject           = $onSubject;
    }

    /**
     * @param ActionInterface        $action        action
     * @param ActionManagerInterface $actionManager actionManager
     */
    public function deploy(ActionInterface $action, ActionManagerInterface $actionManager)
    {
        if ($action->getStatusWanted() !== ActionInterface::STATUS_PUBLISHED) {
            return;
        }

        $this->entryCollection->setActionManager($actionManager);

        $results = $this->processSpreads($action);
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

        $actionManager->updateAction($action);

        $this->entryCollection->clear();
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

    /**
     * @return boolean
     */
    public function isDeliveryImmediate()
    {
        return self::DELIVERY_IMMEDIATE === $this->delivery;
    }

    /**
     * @param SpreadInterface $spread spread
     */
    public function addSpread(SpreadInterface $spread)
    {
        $this->spreads[] = $spread;
    }

    /**
     * @param ActionInterface $action action
     *
     * @return \ArrayIterator
     */
    public function processSpreads(ActionInterface $action)
    {
        if ($this->onSubject) {
            $this->entryCollection->add(new Entry($action->getSubject()), 'GLOBAL');
        }

        foreach ($this->spreads as $spread) {
            if ($spread->supports($action)) {
                $spread->process($action, $this->entryCollection);
            }
        }

        return $this->getEntryCollection();
    }

    /**
     * @return EntryCollection
     */
    public function getEntryCollection()
    {
        return $this->entryCollection;
    }

    /**
     * @return \ArrayIterator of SpreadInterface
     */
    public function getSpreads()
    {
        return $this->spreads;
    }
}
