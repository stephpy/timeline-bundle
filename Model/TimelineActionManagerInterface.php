<?php

namespace Highco\TimelineBundle\Model;

use Highco\TimelineBundle\Model\TimelineAction;

/**
 * TimelineActionManagerInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface TimelineActionManagerInterface
{
    /**
     * @param TimelineAction $timelineAction
     */
    public function updateTimelineAction(TimelineAction $timelineAction);

    /**
     * @param string $model
     * @param array  $oids
     *
     * @return array
     */
    public function getTimelineResultsForModelAndOids($model, array $oids);

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getTimelineWithStatusPublished($limit = 10);
}
