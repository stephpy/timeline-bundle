<?php

namespace Highco\TimelineBundle\Entity;

use Highco\TimelineBundle\Model\TimelineInterface;
use Highco\TimelineBundle\Model\TimelineActionInterface;

/**
 * Timeline entity for Doctrine
 *
 */
class Timeline implements TimelineInterface
{
    /**
     * @var string
     */
    protected $subjectModel;

    /**
     * @var mixed
     */
    protected $subjectId;

    /**
     * @var object
     */
    protected $subject;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var integer
     */
    protected $timelineActionId;

    /**
     * @var TimelineActionInterface
     */
    protected $timelineAction;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    function __construct()
    {
        $this->setCreatedAt(new \DateTime);
    }

    /**
     * {@inheritDoc}
     *
     * @return Timeline
     */
    public static function create($subject, TimelineActionInterface $timelineAction, $context = 'GLOBAL')
    {
        if (!is_object($subject)) {
            throw new \InvalidArgumentException('Subject should be an object');
        }

        $timeline = new static();
        $timeline->setSubject($subject);
        $timeline->setTimelineAction($timelineAction);
        $timeline->setContext($context);
        return $timeline;
    }

    /**
     * @param  object $subject
     *
     * @return void
     */
    public function setSubject($subject)
    {
        if (!is_object($subject)) {
            throw new \InvalidArgumentException('subject must be an object');
        }

        $this->subject = $subject;
        $this->setSubjectModel(get_class($subject));
        $this->setSubjectId($subject->getId());
    }

    /**
     * @return object|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $model
     */
    public function setSubjectModel($model)
    {
        $this->setSubjectModel($model);
    }

    /**
     * @return string
     */
    public function getSubjectModel()
    {
        return $this->subjectModel;
    }

    /**
     * @param  integer $subjectId
     *
     * @return void
     */
    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    /**
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @param string $context
     *
     * @return void
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $id
     *
     * @return void
     */
    public function setTimelineActionId($id)
    {
        $this->timelineActionId = $id;
    }

    /**
     * @return mixed
     */
    public function getTimelineActionId()
    {
        return $this->timelineActionId;
    }

    /**
     * @param TimelineActionInterface $timelineAction
     *
     * @return void
     */
    public function setTimelineAction(TimelineActionInterface $timelineAction)
    {
        $this->timelineAction = $timelineAction;
        $this->setTimelineActionId($timelineAction->getId());
    }

    /**
     * @return TimelineActionInterface|null
     */
    public function getTimelineAction()
    {
        return $this->timelineAction;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return \DateTime
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}
