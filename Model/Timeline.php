<?php

namespace Spy\TimelineBundle\Model;

use Spy\TimelineBundle\Model\TimelineInterface;
use Spy\TimelineBundle\Model\TimelineActionInterface;

/**
 * Timeline model
 *
 */
class Timeline implements TimelineInterface
{
    /**
     * @var Component
     */
    protected $subject;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @param ComplementInterface $subject subject
     *
     * @return Timeline
     */
    public function setSubject(ComplementInterface $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return ComplementInterface
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $context
     *
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
     * @param \DateTime $createdAt
     *
     * @return Timeline
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}
