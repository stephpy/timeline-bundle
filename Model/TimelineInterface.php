<?php

namespace Highco\TimelineBundle\Model;

/**
 * TimelineActionManagerInterface
 */
interface TimelineInterface
{
    /**
     * @param  object $subject
     *
     * @return void
     */
    public function setSubject($subject);

    /**
     * @return object|null
     */
    public function getSubject();

    /**
     * @param string $model
     */
    public function setSubjectModel($model);

    /**
     * @return string
     */
    public function getSubjectModel();

    /**
     * @param  integer $subjectId
     *
     * @return void
     */
    public function setSubjectId($subjectId);

    /**
     * @return integer
     */
    public function getSubjectId();

    /**
     * @param string $context
     *
     * @return void
     */
    public function setContext($context);

    /**
     * @return string
     */
    public function getContext();

    /**
     * @param mixed $id
     *
     * @return void
     */
    public function setTimelineActionId($id);

    /**
     * @return mixed
     */
    public function getTimelineActionId();

    /**
     * @param TimelineActionInterface $timelineAction
     *
     * @return void
     */
    public function setTimelineAction(TimelineActionInterface $timelineAction);

    /**
     * @return TimelineActionInterface|null
     */
    public function getTimelineAction();
}
