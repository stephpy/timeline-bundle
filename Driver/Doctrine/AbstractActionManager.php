<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ManagerRegistry;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\ResultBuilder\ResultBuilderInterface;
use Spy\Timeline\Driver\AbstractActionManager as BaseActionManager;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Driver\Doctrine\ValueObject\ResolvedComponentData;

/**
 * The abstract action manager for doctrine.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractActionManager extends BaseActionManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ResultBuilderInterface
     */
    protected $resultBuilder;

    /**
     * @var ManagerRegistry[]
     */
    protected $registries = array();

    /**
     * @param ObjectManager          $objectManager        objectManager
     * @param ResultBuilderInterface $resultBuilder        resultBuilder
     * @param string                 $actionClass          actionClass
     * @param string                 $componentClass       componentClass
     * @param string                 $actionComponentClass actionComponentClass
     */
    public function __construct(ObjectManager $objectManager, ResultBuilderInterface $resultBuilder, $actionClass, $componentClass, $actionComponentClass)
    {
        $this->objectManager        = $objectManager;
        $this->resultBuilder        = $resultBuilder;

        parent::__construct($actionClass, $componentClass, $actionComponentClass);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(ActionInterface $action)
    {
        $this->objectManager->persist($action);
        $this->objectManager->flush();

        $this->deployActionDependOnDelivery($action);
    }

    /**
     * {@inheritdoc}
     */
    public function createComponent($model, $identifier = null, $flush = true)
    {
        $resolvedComponentData = $this->resolveModelAndIdentifier($model, $identifier);

        return $this->createComponentFromResolvedComponentData($resolvedComponentData, $flush);
    }

    /**
     * {@inheritdoc}
     */
    public function flushComponents()
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function addRegistry(ManagerRegistry $registry)
    {
        $this->registries[] = $registry;
    }

    /**
     * Resolves the model and identifier.
     *
     * This function tries to resolve the model and identifier.
     *
     * When model is a string:
     *  - it uses the given model string as model and the given identifier as identifier
     *
     * When model is an object:
     *  - It checks with doctrine if there is class meta data for the given object class
     *  - If there is class meta data it uses the meta data to retrieve the model and identifier values
     *  - If there is no class meta data
     *      - it uses the get_class function to retrieve the model string name
     *      - it uses the getId method for the object to try and retrieve the identifier
     *
     * @param mixed $model
     * @param mixed $identifier
     *
     * @return ResolvedComponentData
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function resolveModelAndIdentifier($model, $identifier)
    {
        if (!is_object($model) && (null === $identifier || '' === $identifier)) {
            throw new \LogicException('Model has to be an object or a scalar + an identifier in 2nd argument');
        }

        $data = null;

        if (is_object($model)) {
            $data       = $model;
            $modelClass = get_class($model);
            $metadata   = $this->getClassMetadata($modelClass);

            // if object is linked to doctrine
            if (null !== $metadata) {
                $fields     = $metadata->getIdentifier();
                if (!is_array($fields)) {
                    $fields = array($fields);
                }
                $many       = count($fields) > 1;

                $identifier = array();
                foreach ($fields as $field) {
                    $getMethod = sprintf('get%s', ucfirst($field));
                    $value     = (string) $model->{$getMethod}();

                    //Do not use it: https://github.com/stephpy/TimelineBundle/issues/59
                    //$value = (string) $metadata->reflFields[$field]->getValue($model);

                    if (empty($value)) {
                        throw new \Exception(sprintf('Field "%s" of model "%s" return an empty result, model has to be persisted.', $field, $modelClass));
                    }

                    $identifier[$field] = $value;
                }

                if (!$many) {
                    $identifier = current($identifier);
                }

                $model = $metadata->name;
            } else {
                if (!method_exists($model, 'getId')) {
                    throw new \LogicException('Model must have a getId method.');
                }

                $identifier = $model->getId();
                $model      = $modelClass;
            }
        }

        return new ResolvedComponentData($model, $identifier, $data);
    }

    /**
     * @param string $class
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata|null
     */
    protected function getClassMetadata($class)
    {
        foreach ($this->registries as $registry) {
            if ($manager = $registry->getManagerForClass($class)) {
                return $manager->getClassMetadata($class);
            }
        }

        return null;
    }

    /**
     * Creates a component from a resolved model and identifier and optionally stores it to the storage engine.
     *
     * @param ResolvedComponentData $resolved The resolved component data
     * @param boolean               $flush    Whether to flush or not, defaults to true
     *
     * @return ComponentInterface The newly created and populated component
     */
    protected function createComponentFromResolvedComponentData(ResolvedComponentData $resolved, $flush = true)
    {
        $component = $this->getComponentFromResolvedComponentData($resolved);

        $this->objectManager->persist($component);

        if ($flush) {
            $this->flushComponents();
        }

        return $component;
    }

    /**
     * Creates a new component object from the resolved data.
     *
     * @param ResolvedComponentData $resolved The resolved component data
     *
     * @return ComponentInterface The newly created and populated component
     */
    private function getComponentFromResolvedComponentData(ResolvedComponentData $resolved)
    {
        /** @var $component ComponentInterface */
        $component = new $this->componentClass();
        $component->setModel($resolved->getModel());
        $component->setData($resolved->getData());
        $component->setIdentifier($resolved->getIdentifier());

        return $component;
    }
}
