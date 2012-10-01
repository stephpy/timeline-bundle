<?php

namespace Highco\TimelineBundle\Tests\Fixtures;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * TimelineActionManager
 *
 * @uses TimelineActionManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class TimelineActionManager implements TimelineActionManagerInterface
{
    /**
     * @return void
     */
    public function getEntityManager()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function updateTimelineAction(TimelineActionInterface $timelineAction)
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
     * @param array $options
     */
    public function getTimeline(array $params, array $options = array())
    {
        return array();
    }
}
