<?php

namespace Highco\TimelineBundle\Timeline\Spread;

use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Provider\InterfaceProvider;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
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
     * @var InterfaceProvider
     */
    private $provider;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param Manager           $spreadManager
     * @param ObjectManager     $em
     * @param InterfaceProvider $provider
     */
    public function __construct(Manager $spreadManager, ObjectManager $em, InterfaceProvider $provider)
    {
        $this->spreadManager = $spreadManager;
        $this->em            = $em;
        $this->provider      = $provider;
    }

    /**
     * @param TimelineAction $timelineAction
     */
    public function deploy(TimelineAction $timelineAction)
    {
        $this->spreadManager->process($timelineAction);
        $results = $this->spreadManager->getResults();

        if ($timelineAction->getStatusWanted() !== 'published') {
            return;
        }

        foreach ($results as $context => $values) {
            foreach ($values as $entry) {
                $this->provider->add($timelineAction, $context, $entry->subject_model, $entry->subject_id);
            }
        }

        $timelineAction->setStatusCurrent(TimelineAction::STATUS_PUBLISHED);
        $timelineAction->setStatusWanted(TimelineAction::STATUS_FROZEN);

        $this->em->persist($timelineAction);
        $this->em->flush();

        // we have to clear results from spread manager
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
