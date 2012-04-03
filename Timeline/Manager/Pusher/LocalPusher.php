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
     * @param TimelineActionManagerInterface $timelineActionManager Manager to retrieve from local storage
     * @param Deployer                       $deployer              Deploy to notify on deploy on spreads
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
