<?php

namespace Spy\TimelineBundle\Spread;

use Spy\TimelineBundle\Driver\ActionManagerInterface;
use Spy\TimelineBundle\Driver\TimelineManagerInterface;
use Spy\TimelineBundle\Model\TimelineInterface;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Notification\NotificationManager;
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
     * @var ActionManagerInterface
     */
    protected $actionManager;

    /**
     * @var TimelineManagerInterface
     */
    protected $timelineManager;

    /**
     * @param NotificationManager      $notificationManager notificationManager
     * @param ActionManagerInterface   $actionManager       actionManager
     * @param TimelineManagerInterface $timelineManager     timelineManager
     * @param EntryCollection          $entryCollection     entryCollection
     * @param boolean                  $onSubject           onSubject
     */
    public function __construct(NotificationManager $notificationManager, ActionManagerInterface $actionManager, TimelineManagerInterface $timelineManager, EntryCollection $entryCollection, $onSubject = true)
    {
        $this->notificationManager = $notificationManager;
        $this->actionManager       = $actionManager;
        $this->timelineManager     = $timelineManager;
        $this->entryCollection     = $entryCollection;
        $this->spreads             = new \ArrayIterator();
        $this->onSubject           = $onSubject;

        $this->entryCollection->setActionManager($actionManager);
    }

    /**
     * @param ActionInterface $action action
     */
    public function deploy(ActionInterface $action)
    {
        if ($action->getStatusWanted() !== ActionInterface::STATUS_PUBLISHED) {
            return;
        }

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

        $this->actionManager->updateAction($action);

        $this->clear();
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
     * Clears the entryCollection
     */
    public function clear()
    {
        $this->entryCollection->clear();
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
