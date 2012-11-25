<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Spy\TimelineBundle\Model\Component;
use \DateTime;

interface ActionInterface
{
    public function getSpreadTime();
    public function isPublished();
    public function hasDuplicateKey();
    public function setIsDuplicated($duplicated);
    public function isDuplicated();

    /**
     * @param string           $type                 type
     * @param string|Component $component            component
     * @param string           $actionComponentClass actionComponentClass
     *
     * @return ActionInterface
     */
    public function addComponent($type, $component, $actionComponentClass);

    public function getId();
    public function setId($id);
    public function setVerb($verb);
    public function getVerb();
    public function setStatusCurrent($statusCurrent);
    public function getStatusCurrent();
    public function setStatusWanted($statusWanted);
    public function getStatusWanted();
    public function setDuplicateKey($duplicateKey);
    public function getDuplicateKey();
    public function setDuplicatePriority($duplicatePriority);
    public function getDuplicatePriority();
    public function setCreatedAt(DateTime $createdAt);
    public function getCreatedAt();
    public function getActionComponents();
}
