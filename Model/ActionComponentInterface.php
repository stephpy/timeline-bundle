<?php

namespace Spy\TimelineBundle\Model;

/**
 * ActionComponentInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ActionComponentInterface
{
    /**
     * @param string $id id
     * @return ActionComponentInterface
     */
    public function setId($id);

    /**
     * @return integer
     */
    public function getId();

    /**
     * @param string $type
     * @return ActionComponent
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $text
     * @return ActionComponent
     */
    public function setText($text);

    /**
     * @return string
     */
    public function getText();

    /**
     * @param ActionInterface $action
     * @return ActionComponent
     */
    public function setAction(ActionInterface $action);

    /**
     * @return ActionInterface
     */
    public function getAction();

    /**
     * @param ComponentInterface $component
     * @return ActionComponent
     */
    public function setComponent(ComponentInterface $component);

    /**
     * @return ComponentInterface
     */
    public function getComponent();
}
