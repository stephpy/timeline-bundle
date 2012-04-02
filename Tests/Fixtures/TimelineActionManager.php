<?php

namespace Highco\TimelineBundle\Tests\Fixtures;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Doctrine\Common\Persistence\ObjectManager;

class TimelineActionManager implements TimelineActionManagerInterface
{
    /**
     * {@inheritedDoc}
     */
    public function updateTimelineAction(TimelineAction $timelineAction)
    {
    }

    /**
     * {@inheritedDoc}
     */
    public function getTimelineWithStatusPublished($limit = 10)
    {
        return array();
    }

    /**
     * {@inheritedDoc}
     */
    public function getTimelineActionsForIds(array $ids)
    {
        return array();
    }

    /**
     * {@inheritedDoc}
     */
    public function getTimelineResultsForModelAndOids($model, array $oids)
    {
        return array();
    }

    /**
     * {@inheritedDoc}
     */
    public function getTimeline(array $params, array $options = array())
    {
        return array();
    }
}
