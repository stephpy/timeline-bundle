<?php

namespace Highco\TimelineBundle\Timeline\Manager\Pusher;

use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Deployer;

/**
 * @uses PusherInterface
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class LocalPusher implements PusherInterface
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var Deployer
     */
    private $deployer;

    /**
     * @param ObjectManager $em
     * @param Deployer      $deployer
     */
    public function __construct(ObjectManager $em, Deployer $deployer)
    {
        $this->em       = $em;
        $this->deployer = $deployer;
    }

    /**
     * @param TimelineAction $timelineAction
     *
     * @return boolean
     */
    public function push(TimelineAction $timelineAction)
    {
        $this->em->persist($timelineAction);
        $this->em->flush();

        if ($this->deployer->getDelivery() == Deployer::DELIVERY_IMMEDIATE) {
            $this->deployer->deploy($timelineAction);
            return true;
        }

        return false;
    }
}
