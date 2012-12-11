<?php

namespace Spy\TimelineBundle\Model;

/**
 * TimelineInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface TimelineInterface
{
    CONST TYPE_TIMELINE = 'timeline';

    /**
     * {@inheritdoc}
     */
    public function setId($id);

    /**
     * {@inheritdoc}
     */
    public function getId();

    /**
     * @param string $context
     * @return Timeline
     */
    public function setContext($context);

    /**
     * @return string
     */
    public function getContext();

    /**
     * @param string $type
     * @return Timeline
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param \DateTime $createdAt
     * @return Timeline
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param Component $subject
     * @return Timeline
     */
    public function setSubject(ComponentInterface $subject);

    /**
     * @return ComponentInterface
     */
    public function getSubject();

    /**
     * @param ActionInterface $action
     * @return Timeline
     */
    public function setAction(ActionInterface $action);

    /**
     * @return ActionInterface
     */
    public function getAction();
}
