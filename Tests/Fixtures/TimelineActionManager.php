<?php

namespace Highco\TimelineBundle\Tests\Fixtures;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Model\TimelineAction;
use Doctrine\Common\Persistence\ObjectManager;

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
    public function getTimeline(array $params, array $options = array())
    {
        return array();
    }
}
