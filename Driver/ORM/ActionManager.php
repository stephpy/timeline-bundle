<?php

namespace Spy\TimelineBundle\Driver\ORM;

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
     * @param ObjectManager $objectManager  object manager
     * @param string        $actionClass    action class
     * @param string        $componentClass component class
     */
    public function __construct(ObjectManager $objectManager, $actionClass, $componentClass)
    {
        $this->objectManager  = $objectManager;
        $this->actionClass    = $actionClass;
        $this->componentClass = $componentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrCreateComponent($model, $identifier = null)
    {
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
