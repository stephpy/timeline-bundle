<?php

namespace Highco\TimelineBundle\Tests\Fixtures;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Doctrine\Common\Persistence\ObjectManager;

class TimelineActionManager implements TimelineActionManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function updateTimelineAction(TimelineAction $timelineAction)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineWithStatusPublished($limit = 10)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineActionsForIds(array $ids)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimelineResultsForModelAndOids($model, array $oids)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeline(array $params, array $options = array())
    {
        return array();
    }
}
