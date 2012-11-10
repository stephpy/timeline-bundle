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
     * @param  TimelineActionInterface $timelineAction
     * @return void
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
     * Get a count of timeline entries for a subject
     *
     * @param array $params  (subjectModel, subjectId)
     * @param array $options (offset, limit, status)
     *
     * @return array
     */
    public function countTimeline(array $params, array $options = array());

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
