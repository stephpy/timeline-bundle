<?php

namespace Spy\TimelineBundle\Model;

/**
 * Timeline
 *
 * @uses TimelineInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Timeline implements TimelineInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $type = self::TYPE_TIMELINE;

    /**
     * @var Component
     */
    protected $subject;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @{inheritdoc}
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @{inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @{inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @{inheritdoc}
     */
    public function setSubject(ComponentInterface $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @{inheritdoc}
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @{inheritdoc}
     */
    public function getAction()
    {
        return $this->action;
    }
}
