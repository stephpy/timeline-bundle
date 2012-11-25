<?php

namespace Spy\TimelineBundle\Model;

use \DateTime;

/**
 * Timeline model
 *
 */
class Timeline
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
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var Component
     */
    protected $subject;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $context
     * @return Timeline
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param DateTime $createdAt
     * @return Timeline
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Component $subject
     * @return Timeline
     */
    public function setSubject(Component $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Component
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
