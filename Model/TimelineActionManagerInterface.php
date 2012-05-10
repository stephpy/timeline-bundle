<?php

namespace Highco\TimelineBundle\Model;

use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * TimelineActionManagerInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface TimelineActionManagerInterface
{
    /**
     * @param TimelineActionInterface $timelineAction
     */
    public function updateTimelineAction(TimelineActionInterface $timelineAction);

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getTimelineWithStatusPublished($limit = 10);

    /**
     * @param array $ids
     *
     * @return array
     */
    public function getTimelineActionsForIds(array $ids);

    /**
     * getTimeline of a subject
     *
     * @param array $params  (subjectModel, subjectId)
     * @param array $options (offset, limit, status)
     *
     * @return array
     */
    public function getTimeline(array $params, array $options = array());
}
