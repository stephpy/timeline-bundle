<?php

namespace Spy\TimelineBundle\Driver\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ManagerRegistry;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\ResultBuilder\ResultBuilderInterface;
use Spy\Timeline\Driver\AbstractActionManager as BaseActionManager;
use Spy\Timeline\Model\ComponentInterface;
<<<<<<< HEAD
use Spy\TimelineBundle\Driver\Doctrine\ValueObject\ResolvedComponentData;
=======
>>>>>>> 201164927cc2a56a70104b47e9ea702a090f5d67

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
     * @var ManagerRegistry[]
     */
    protected $registries;

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
        $this->actionClass          = $actionClass;
        $this->componentClass       = $componentClass;
        $this->actionComponentClass = $actionComponentClass;
        $this->registries           = array();
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

<<<<<<< HEAD
        return $this->createComponentFromResolvedComponentData($resolvedComponentData, $flush);
=======
        return $this->createComponentFromResolvedModelAndIdentifier($model, $identifier, $data, $flush);
>>>>>>> 201164927cc2a56a70104b47e9ea702a090f5d67
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
<<<<<<< HEAD
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
=======
     * @param mixed $model
     * @param mixed $identifier
     *
     * @return array array(model, identifier, data)
     *
>>>>>>> 201164927cc2a56a70104b47e9ea702a090f5d67
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

<<<<<<< HEAD
        return new ResolvedComponentData($model, $identifier, $data);
=======
        if (is_scalar($identifier)) {
            $identifier = (string) $identifier;
        } elseif (!is_array($identifier)) {
            throw new \InvalidArgumentException('Identifier has to be a scalar or an array');
        }

        $this->guardResolvedModelAndIdentifier($model, $identifier);

        return array($model, $identifier, $data);
>>>>>>> 201164927cc2a56a70104b47e9ea702a090f5d67
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
<<<<<<< HEAD
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
=======
     * Creates a component from a resolved model and identifier.
     *
     * This function assumes the model and identifier has been resolved.
     *
     * @param string       $model      The resolved model string
     * @param string|array $identifier The resolved identifier string or array
     * @param object|null  $data       If the model is an object we set the object as data else this is null
     * @param boolean      $flush      Whether to flush or not, defaults to true
     *
     * @return ComponentInterface The created component
     */
    protected function createComponentFromResolvedModelAndIdentifier($model, $identifier, $data, $flush = true)
    {
        $component = $this->getComponentFromResolvedModelAndIdentifier($model, $identifier, $data);
>>>>>>> 201164927cc2a56a70104b47e9ea702a090f5d67

        $this->objectManager->persist($component);

        if ($flush) {
            $this->flushComponents();
        }

        return $component;
    }

    /**
<<<<<<< HEAD
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
=======
     * Creates the component from the resolved model and identifier.
     *
     * @param string       $model      The model string
     * @param string|array $identifier The identifier string or array
     * @param object|null  $data       The object when given or null
     *
     * @return ComponentInterface The populated component
     */
    private function getComponentFromResolvedModelAndIdentifier($model, $identifier, $data)
    {
        /** @var $component ComponentInterface */
        $component = new $this->componentClass();
        $component->setModel($model);
        $component->setData($data);
        $component->setIdentifier($identifier);

        return $component;
    }

    /**
     * Guards that there is a model and identifier after we should have resolved those.
     *
     * @param string       $model
     * @param string|array $identifier
     *
     * @throws \Exception
     */
    private function guardResolvedModelAndIdentifier($model, $identifier)
    {
        if (empty($model) || null === $identifier || '' === $identifier) {
            if (is_array($identifier)) {
                $identifier = implode(', ', $identifier);
            }

            throw new \Exception(sprintf('To find a component, you have to give a model (%s) and an identifier (%s)', $model, $identifier));
        }
    }
>>>>>>> 201164927cc2a56a70104b47e9ea702a090f5d67
}
