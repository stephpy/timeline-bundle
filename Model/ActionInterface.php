<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Spy\TimelineBundle\Model\ComponentInterface;

/**
 * ActionInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ActionInterface
{
    CONST STATUS_PENDING   = 'pending';
    CONST STATUS_PUBLISHED = 'published';
    CONST STATUS_FROZEN    = 'frozen';

    /**
     * @param string                    $type                 type
     * @param string|ComponentInterface $component            component
     * @param string                    $actionComponentClass actionComponentClass
     *
     * @return ActionInterface
     */
    public function addComponent($type, $component, $actionComponentClass);

    /**
     * @return integer
     */
    public function getSpreadTime();

    /**
     * @return boolean
     */
    public function isPublished();

    /**
     * @return boolean
     */
    public function hasDuplicateKey();

    /**
     * @param boolean $duplicated duplicated
     *
     * @return ActionInterface
     */
    public function setIsDuplicated($duplicated);

    /**
     * @return boolean
     */
    public function isDuplicated();

    /**
     * @param string $type type
     *
     * @return ComponentInterface|null
     */
    public function getComponent($type);

    /**
     * @param ComponentInterface  $component            component
     * @param string              $actionComponentClass actionComponentClass
     *
     * @return ActionInterface
     */
    public function setSubject(ComponentInterface $component, $actionComponentClass);

    /**
     * @return ComponentInterface|null
     */
    public function getSubject();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id id
     *
     * @return ActionInterface
     */
    public function setId($id);

    /**
     * @param string$verb verb
     *
     * @return ActionInterface
     */
    public function setVerb($verb);

    /**
     * @return string
     */
    public function getVerb();

    /**
     * @param string $statusCurrent statusCurrent
     *
     * @return ActionInterface
     */
    public function setStatusCurrent($statusCurrent);

    /**
     * @return string
     */
    public function getStatusCurrent();

    /**
     * @param string $statusWanted statusWanted
     *
     * @return ActionInterface
     */
    public function setStatusWanted($statusWanted);

    /**
     * @return string
     */
    public function getStatusWanted();

    /**
     * @param string $duplicateKey duplicateKey
     *
     * @return ActionInterface
     */
    public function setDuplicateKey($duplicateKey);

    /**
     * @return string
     */
    public function getDuplicateKey();

    /**
     * @param integer $duplicatePriority duplicatePriority
     *
     * @return ActionInterface
     */
    public function setDuplicatePriority($duplicatePriority);

    /**
     * @return integer
     */
    public function getDuplicatePriority();

    /**
     * @param \DateTime $createdAt createdAt
     *
     * @return ActionInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return ArrayCollection
     */
    public function getActionComponents();
}
