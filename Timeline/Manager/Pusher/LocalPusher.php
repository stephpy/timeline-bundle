<?php

namespace Highco\TimelineBundle\Timeline\Manager\Pusher;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Highco\TimelineBundle\Timeline\Spread\Deployer;

/**
 * Push on local by using provider
 *
 * @uses PusherInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class LocalPusher implements PusherInterface
{
    /**
     * @var TimelineActionManagerInterface
     */
    private $timelineActionManager;

    /**
     * @var Deployer
     */
    private $deployer;

    /**
     * @param TimelineActionManagerInterface $timelineActionManager
     * @param Deployer                       $deployer
     */
    public function __construct(TimelineActionManagerInterface $timelineActionManager, Deployer $deployer)
    {
        $this->timelineActionManager = $timelineActionManager;
        $this->deployer              = $deployer;
    }

    /**
     * {@inheritDoc}
     */
    public function push(TimelineAction $timelineAction)
    {
        $this->timelineActionManager->updateTimelineAction($timelineAction);

        if ($this->deployer->getDelivery() == Deployer::DELIVERY_IMMEDIATE) {
            $this->deployer->deploy($timelineAction);

            return true;
        }

        return false;
    }
}
