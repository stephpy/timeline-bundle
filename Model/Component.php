<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Component
 *
 * @uses ComponentInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Component implements ComponentInterface
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
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function addActionComponent(ActionComponent $actionComponents)
    {
        $this->actionComponents[] = $actionComponents;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeActionComponent(ActionComponent $actionComponents)
    {
        $this->actionComponents->removeElement($actionComponents);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionComponents()
    {
        return $this->actionComponents;
    }
}
