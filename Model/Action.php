<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;

class Action
{
    CONST STATUS_PENDING   = 'pending';
    CONST STATUS_PUBLISHED = 'published';
    CONST STATUS_FROZEN    = 'frozen';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var string
     */
    protected $statusCurrent = self::STATUS_PENDING;

    /**
     * @var string
     */
    protected $statusWanted = self::STATUS_PUBLISHED;

    /**
     * @var string
     */
    protected $duplicateKey;

    /**
     * @var integer
     */
    protected $duplicatePriority;

    /**
     * @var boolean
     */
    protected $dupplicated = false;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @var ArrayCollection
     */
    protected $actionComponents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt        = new DateTime();
        $this->actionComponents = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getSpreadTime()
    {
        return time();
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return $this->statusCurrent == self::STATUS_PUBLISHED;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDuplicateKey()
    {
        return null !== $this->duplicateKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDuplicated($duplicated)
    {
        $this->duplicated = (bool) $duplicated;
    }

    /**
     * {@inheritdoc}
     */
    public function isDuplicated()
    {
        return (bool) $this->duplicated;
    }

    /**
     * @return array
     */
    public function getValidStatus()
    {
        return array(
            self::STATUS_PENDING,
            self::STATUS_PUBLISHED,
            self::STATUS_FROZEN,
        );
    }

    /**
     * @param string $status
     *
     * @return boolean
     */
    public function isValidStatus($status)
    {
        return in_array((string) $status, $this->getValidStatus());
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $verb
     * @return Action
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param string $statusCurrent
     * @return Action
     */
    public function setStatusCurrent($statusCurrent)
    {
        if (!$this->isValidStatus($statusCurrent)) {
            throw new \InvalidArgumentException(sprintf('Status "%s" is not valid, (%s)', $statusCurrent, implode(', ', $this->getValidStatus())));
        }

        $this->statusCurrent = $statusCurrent;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusCurrent()
    {
        return $this->statusCurrent;
    }

    /**
     * @param string $statusWanted
     * @return Action
     */
    public function setStatusWanted($statusWanted)
    {
        if (!$this->isValidStatus($statusWanted)) {
            throw new \InvalidArgumentException(sprintf('Status "%s" is not valid, (%s)', $statusWanted, implode(', ', $this->getValidStatus())));
        }

        $this->statusWanted = $statusWanted;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusWanted()
    {
        return $this->statusWanted;
    }

    /**
     * @param string $duplicateKey
     * @return Action
     */
    public function setDuplicateKey($duplicateKey)
    {
        $this->duplicateKey = $duplicateKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getDuplicateKey()
    {
        return $this->duplicateKey;
    }

    /**
     * @param integer $duplicatePriority
     * @return Action
     */
    public function setDuplicatePriority($duplicatePriority)
    {
        $this->duplicatePriority = (int) $duplicatePriority;

        return $this;
    }

    /**
     * @return integer
     */
    public function getDuplicatePriority()
    {
        return (int) $this->duplicatePriority;
    }

    /**
     * @param DateTime $createdAt
     * @return Action
     */
    public function setCreatedAt(DateTime $createdAt)
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
     * @param ActionComponent $actionComponents
     * @return Action
     */
    public function addActionComponent(ActionComponent $actionComponents)
    {
        $this->actionComponents[] = $actionComponents;

        return $this;
    }

    /**
     * @param ActionComponent $actionComponents
     */
    public function removeActionComponent(ActionComponent $actionComponents)
    {
        $this->actionComponents->removeElement($actionComponents);
    }

    /**
     * @return ArrayCollection
     */
    public function getActionComponents()
    {
        return $this->actionComponents;
    }

}