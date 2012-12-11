<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ComponentInterface;

/**
 * ActionManagerInterface
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ActionManagerInterface
{
    /**
     * @param int $limit limit
     *
     * @return array
     */
    public function findActionsWithStatusWantedPublished($limit = 100);

    /**
     * @param ComponentInterface $subject subject
     * @param array              $status  status
     *
     * @return integer
     */
    public function countActions(ComponentInterface $subject, $status = ActionInterface::STATUS_PUBLISHED);

    /**
     * @param ComponentInterface $subject subject
     * @param array              $options offset, limit, status
     *
     * @return array
     */
    public function getSubjectActions(ComponentInterface $subject, array $options = array());

    /**
     * @param ActionInterface $action action
     */
    public function updateAction(ActionInterface $action);

    /**
     * @param object $subject    Can be a ComponentInterface or an other one object.
     * @param string $verb       verb
     * @param array  $components An array of ComponentInterface or other objects.
     *
     * @return Action
     */
    public function create($subject, $verb, array $components = array());

    /**
     * Find a component or create it.
     *
     * @param string|object     $model      pass an object and second argument will be ignored.
     * it'll be replaced by $model->getId();
     * @param null|string|array $identifier pass an array for composite keys.
     * @param boolean           $flush      is component flushed with this method ?
     *
     * @return ComponentInterface
     */
    public function findOrCreateComponent($model, $identifier = null, $flush = true);

    /**
     * create component.
     *
     * @param string|object     $model      pass an object and second argument will be ignored.
     * it'll be replaced by $model->getId();
     * @param null|string|array $identifier pass an array for composite keys.
     * @param boolean           $flush      is component flushed with this method ?
     *
     * @return ComponentInterface
     */
    public function createComponent($model, $identifier = null, $flush = true);

    /**
     * flushComponents
     */
    public function flushComponents();

    /**
     * @param array $concatIdents array<concat(model,identifier)>
     *
     * @return array
     */
    public function findComponents(array $concatIdents);
}
