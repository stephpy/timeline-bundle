<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Component
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var array
     */
    protected $identifier;

    /**
     * @var ArrayCollection
     */
    protected $actionComponents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->actionComponents = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $model
     * @return Component
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $identifier
     * @return Component
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param ActionComponent $actionComponents
     * @return Component
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
