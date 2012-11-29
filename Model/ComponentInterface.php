<?php

namespace Spy\TimelineBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ComponentInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ComponentInterface
{
    /**
     * Return unique hash for this component.
     *
     * @return string
     */
    public function getHash();

    /**
     * @param mixed $data data
     *
     * @return Component
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param mixed $id id
     *
     * @return ComponentInterface
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param string $model model
     *
     * @return ComponentInterface
     */
    public function setModel($model);

    /**
     * @return string
     */
    public function getModel();

    /**
     * @param string $identifier identifier
     *
     * @return ComponentInterface
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param ActionComponent $actionComponents actionComponents
     *
     * @return ComponentInterface
     */
    public function addActionComponent(ActionComponent $actionComponents);

    /**
     * @param ActionComponent $actionComponents actionComponents
     *
     * @return ComponentInterface
     */
    public function removeActionComponent(ActionComponent $actionComponents);

    /**
     * @return ArrayCollection
     */
    public function getActionComponents();
}
