<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ComponentInterface;
use Spy\TimelineBundle\Driver\ActionManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * ActionManager
 *
 * @uses ActionManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ActionManager implements ActionManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $actionClass;

    /**
     * @var string
     */
    protected $componentClass;

    /**
     * @var string
     */
    protected $actionComponentClass;

    /**
     * @param ObjectManager $objectManager        objectManager
     * @param string        $actionClass          actionClass
     * @param string        $componentClass       componentClass
     * @param string        $actionComponentClass actionComponentClass
     */
    public function __construct(ObjectManager $objectManager, $actionClass, $componentClass, $actionComponentClass)
    {
        $this->objectManager        = $objectManager;
        $this->actionClass          = $actionClass;
        $this->componentClass       = $componentClass;
        $this->actionComponentClass = $actionComponentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(ActionInterface $action)
    {
        $this->objectManager->persist($action);
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function create($subject, $verb, array $components = array())
    {
        $action = new $this->actionClass();
        $action->addComponent('subject', $subject, $this->actionComponentClass);
        $action->setVerb($verb);

        // subject is MANDATORY. Cannot pass scalar value.
        if (!$subject instanceof ComponentInterface) {
            if (!is_object($subject)) {
                $subject = $this->findOrCreateComponent($subject);
            }

            if (null === $subject) {
                throw new \Exception('Impossible to create component from subject.');
            }
        }

        $action->setSubject($subject, $this->actionComponentClass);

        foreach ($components as $type => $component) {
            if (!$component instanceof ComponentInterface && !is_scalar($component)) {
                $component = $this->findOrCreateComponent($component);

                if (null === $component) {
                    throw new \Exception(sprintf('Impossible to create component from %s.', $type));
                }
            }

            $action->addComponent($type, $component, $this->actionComponentClass);
        }

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null)
    {
        if (!is_object($model) && empty($identifier)) {
            throw new \LogicException('Model has to be an object or a scalar + an identifier in 2nd argument');
        }

        if (is_object($model)) {
            if (!method_exists($model, 'getId')) {
                throw new \LogicException('Model must have a getId method.');
            }

            $identifier = $model->getId();
            $model      = Doctrine::unsetProxyClass($model);
        }

        if (empty($model) || empty($identifier)) {
            return null;
        }

        $component = $this->getComponentRepository()
            ->createQueryBuilder('c')
            ->where('c.model = :model')
            ->andWhere('c.identifier = :identifier')
            ->setParameter('model', $model)
            ->setParameter('identifier', serialize($identifier))
            ->getQuery()
            ->getOneOrNullResult()
            ;

        if ($component) {
            return $component;
        }

        $component = new $this->componentClass();
        $component->setModel($model);
        $component->setIdentifier($identifier);

        $this->objectManager->persist($component);
        $this->objectManager->flush();

        return $component;
    }

    protected function getComponentRepository()
    {
        return $this->objectManager->getRepository($this->componentClass);
    }
}
