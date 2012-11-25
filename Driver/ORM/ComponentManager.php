<?php

namespace Spy\TimelineBundle\Driver\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Spy\TimelineBundle\Driver\ComponentManagerInterface;
use Spy\TimelineBundle\Tools\Doctrine;

/**
 * ComponentManager
 *
 * @uses ComponentManagerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ComponentManager implements ComponentManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $componentClass;

    /**
     * @param ObjectManager $objectManager  objectManager
     * @param string        $componentClass componentClass
     */
    public function __construct(ObjectManager $objectManager, $componentClass)
    {
        $this->objectManager  = $objectManager;
        $this->componentClass = $componentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOrCreate($model, $identifier = null)
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

        $component = $this->getRepository()
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

    protected function getRepository()
    {
        return $this->objectManager->getRepository($this->componentClass);
    }
}
