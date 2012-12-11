<?php

namespace Spy\TimelineBundle\Spread;

use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Driver\ActionManagerInterface;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Spread\DeployerInterface;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Notification\NotificationManagerInterface;
use Spy\Timeline\Spread\Entry\Entry;
use Spy\Timeline\Spread\Entry\EntryCollection;

/**
 * Deployer
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Deployer implements DeployerInterface
{
    /**
     * @var \ArrayIterator
     */
    protected $spreads;

    /**
     * @var integer
     */
    protected $batchSize;

    /**
     * @var EntryCollection
     */
    protected $entryCollection;

    /**
     * @var boolean
     */
    protected $onSubject;

    /**
     * @var NotificationManagerInterface
     */
    protected $notificationManager;

    /**
     * @var TimelineManagerInterface
     */
    protected $timelineManager;

    /**
     * @param NotificationManagerInterface $notificationManager notificationManager
     * @param TimelineManagerInterface     $timelineManager     timelineManager
     * @param EntryCollection              $entryCollection     entryCollection
     * @param boolean                      $onSubject           onSubject
     * @param integer                      $batchSize           batch size
     */
    public function __construct(NotificationManagerInterface $notificationManager, TimelineManagerInterface $timelineManager, EntryCollection $entryCollection, $onSubject = true, $batchSize = 50)
    {
        $this->notificationManager = $notificationManager;
        $this->timelineManager     = $timelineManager;
        $this->entryCollection     = $entryCollection;
        $this->spreads             = new \ArrayIterator();
        $this->onSubject           = $onSubject;
        $this->batchSize           = (int) $batchSize;
    }

    /**
     * {@inheritdoc}
     */
    public function deploy(ActionInterface $action, ActionManagerInterface $actionManager)
    {
        if ($action->getStatusWanted() !== ActionInterface::STATUS_PUBLISHED) {
            return;
        }

        $this->entryCollection->setActionManager($actionManager);

        $results = $this->processSpreads($action);
        $results->loadUnawareEntries();

        $i = 1;
        foreach ($results as $context => $entries) {
            foreach ($entries as $entry) {
                $this->timelineManager->createAndPersist($action, $entry->getSubject(), $context, TimelineInterface::TYPE_TIMELINE);
                $this->notificationManager->notify($action, $context, $entry->getSubject());

                if (($i % $this->batchSize) == 0) {
                    $this->timelineManager->flush();
                }
                $i++;
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function isDeliveryImmediate()
    {
        return self::DELIVERY_IMMEDIATE === $this->delivery;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getSpreads()
    {
        return $this->spreads;
    }
}
