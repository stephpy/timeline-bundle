<?php

namespace Spy\TimelineBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * Action
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Action implements ActionInterface
{
    CONST STATUS_PENDING   = 'pending';
    CONST STATUS_PUBLISHED = 'published';
    CONST STATUS_FROZEN    = 'frozen';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var array
     */
    protected $actionComponents = array();

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
    protected $duplicated = false;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Initialize createdAt, statusCurrent and statusWanted property
     */
    public function __construct()
    {
        $this->createdAt     = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * {@inheritdoc}
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    /**
     * {@inheritdoc}
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param string $status
     *
     * @return boolean
     */
    public function isValidStatus($status)
    {
        return in_array((string) $status, array(
            self::STATUS_PENDING,
            self::STATUS_PUBLISHED,
            self::STATUS_FROZEN,
        ));
    }

    /**
     * @param string $statusCurrent
     */
    public function setStatusCurrent($statusCurrent)
    {
        if (!$this->isValidStatus($statusCurrent)) {
            throw new \InvalidArgumentException('Status "'.$statusCurrent.'" is not valid');
        }

        $this->statusCurrent = $statusCurrent;
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
     */
    public function setStatusWanted($statusWanted)
    {
        if (!$this->isValidStatus($statusWanted)) {
            throw new \InvalidArgumentException('Status "'.$statusWanted.'" is not valid');
        }

        $this->statusWanted = $statusWanted;
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
     */
    public function setDuplicateKey($duplicateKey)
    {
        $this->duplicateKey = $duplicateKey;
    }

    /**
     * @return string
     * {@inheritdoc}
     */
    public function getDuplicateKey()
    {
        return $this->duplicateKey;
    }

    /**
     * @param integer $duplicatePriority
     * {@inheritdoc}
     */
    public function setDuplicatePriority($duplicatePriority)
    {
        $this->duplicatePriority = (int) $duplicatePriority;
    }

    /**
     * {@inheritdoc}
     */
    public function getDuplicatePriority()
    {
        return (int) $this->duplicatePriority;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array<string>
     */
    public function __sleep()
    {
        return array(
            'id',
            'verb',
            'statusCurrent',
            'statusWanted',
            'duplicateKey',
            'duplicatePriority',
            'createdAt',
        );
    }
}
