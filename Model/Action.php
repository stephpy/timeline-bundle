<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Spy\TimelineBundle\Model\Component;
use \DateTime;

/**
 * Action
 *
 * @uses ActionInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Action implements ActionInterface
{
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
    protected $duplicated = false;

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
        $this->statusCurrent    = self::STATUS_PENDING;
        $this->statusWanted     = self::STATUS_PUBLISHED;
        $this->createdAt        = new DateTime();
        $this->actionComponents = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addComponent($type, $component, $actionComponentClass)
    {
        $actionComponent = new $actionComponentClass();
        $actionComponent->setType($type);

        if ($component instanceof ComponentInterface) {
            $actionComponent->setComponent($component);
        } elseif(is_scalar($component)) {
            $actionComponent->setText($component);
        } else {
            throw new \InvalidArgumentException('Component has to be a ComponentInterface or a scalar');
        }

        $this->addActionComponent($actionComponent);

        return $this;
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
    public function getValidStatus()
    {
        return array(
            self::STATUS_PENDING,
            self::STATUS_PUBLISHED,
            self::STATUS_FROZEN,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isValidStatus($status)
    {
        return in_array((string) $status, $this->getValidStatus());
    }

    /**
     * @param string $type type
     *
     * @return ComponentInterface|null
     */
    public function getComponent($type)
    {
        foreach ($this->getActionComponents() as $actionComponent) {
            if ($actionComponent->getType() == $type) {
                return $actionComponent->getText() ?: $actionComponent->getComponent();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(ComponentInterface $component, $actionComponentClass)
    {
        $this->addComponent('subject', $component, $actionComponentClass);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->getComponent('subject');
    }

    /**
     * @return boolean
     */
    public function hasComponentHydrated()
    {
        foreach ($this->getActionComponents() as $actionComponent) {
            if (!$actionComponent->isText() &&
                null === $actionComponent->getComponent()->getData()) {
                    return false;
            }
        }

        return true;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getStatusCurrent()
    {
        return $this->statusCurrent;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getStatusWanted()
    {
        return $this->statusWanted;
    }

    /**
     * {@inheritdoc}
     */
    public function setDuplicateKey($duplicateKey)
    {
        $this->duplicateKey = $duplicateKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDuplicateKey()
    {
        return $this->duplicateKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setDuplicatePriority($duplicatePriority)
    {
        $this->duplicatePriority = (int) $duplicatePriority;

        return $this;
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param ActionComponentInterface $actionComponents
     * @return Action
     */
    public function addActionComponent(ActionComponentInterface $actionComponents)
    {
        $actionComponents->setAction($this);
        $this->actionComponents[] = $actionComponents;

        return $this;
    }

    /**
     * @param ActionComponentInterface $actionComponents
     */
    public function removeActionComponent(ActionComponentInterface $actionComponents)
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
