<?php

namespace Spy\TimelineBundle\ResolveComponent;

use Spy\Timeline\ResolveComponent\ValueObject\ResolvedComponentData;
use Spy\Timeline\Exception\ResolveComponentDataException;
use Spy\Timeline\ResolveComponent\ComponentDataResolverInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Spy\Timeline\ResolveComponent\ValueObject\ResolveComponentModelIdentifier;

/**
 * When model is a string:
 * - it uses the given model string as model and the given identifier as identifier
 *
 * When model is an object:
 * - It checks with doctrine if there is class meta data for the given object class
 * - If there is class meta data it uses the meta data to retrieve the model and identifier values
 * - If there is no class meta data
 * - it uses the get_class function to retrieve the model string name
 * - it uses the getId method for the object to try and retrieve the identifier
 */
class DoctrineComponentDataResolver implements ComponentDataResolverInterface
{
    /**
     * @var ManagerRegistry[]
     */
    protected $registries;

    /**
     * {@inheritdoc}
     */
    public function resolveComponentData(ResolveComponentModelIdentifier $resolve)
    {
        $model = $resolve->getModel();
        $identifier = $resolve->getIdentifier();
        $data = null;

        if (is_object($model)) {
            $data = $model;
            $modelClass = get_class($model);
            $metadata = $this->getClassMetadata($modelClass);

            // if object is linked to doctrine
            if (null !== $metadata) {
                $fields = $metadata->getIdentifier();
                if (!is_array($fields)) {
                    $fields = array($fields);
                }
                $many = count($fields) > 1;

                $identifier = array();
                foreach ($fields as $field) {
                    $getMethod = sprintf('get%s', ucfirst($field));
                    $value = (string) $model->{$getMethod}();

                    //Do not use it: https://github.com/stephpy/TimelineBundle/issues/59
                    //$value = (string) $metadata->reflFields[$field]->getValue($model);

                    if (empty($value)) {
                        throw new ResolveComponentDataException(sprintf('Field "%s" of model "%s" return an empty result, model has to be persisted.', $field, $modelClass));
                    }

                    $identifier[$field] = $value;
                }

                if (!$many) {
                    $identifier = current($identifier);
                }

                $model = $metadata->getName();
            } else {
                if (!method_exists($model, 'getId')) {
                    throw new ResolveComponentDataException('Model must have a getId method.');
                }

                $identifier = $model->getId();
                $model = $modelClass;
            }
        }

        return new ResolvedComponentData($model, $identifier, $data);
    }

    /**
     * @param ManagerRegistry $manager
     */
    public function addRegistry(ManagerRegistry $manager)
    {
        $this->registries[] = $manager;
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

        return;
    }
}
