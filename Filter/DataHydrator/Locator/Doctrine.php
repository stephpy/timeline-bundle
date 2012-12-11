<?php

namespace Spy\TimelineBundle\Filter\DataHydrator\Locator;

use Spy\Timeline\Model\ComponentInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Doctrine
 *
 * @uses LocatorInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Doctrine implements LocatorInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model)
    {
        try {
            $objectManager = $this->registry->getManagerForClass($model);

            return null !== $objectManager;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function locate($model, array $components)
    {
        $objectManager = $this->registry->getManagerForClass($model);
        $metadata      = $objectManager->getClassMetadata($model);

        $fields     = $metadata->getIdentifier();
        $many       = count($fields) > 1;

        $oids = array();
        foreach ($components as $component) {
            $oids[] = $component->getIdentifier();
        }

        if (count($fields) > 1) {
            return $this->locateComposite($objectManager, $metadata, $model, $components, $oids, $fields);
        }

        $field = current($fields);
        $qb    = $objectManager->getRepository($model)
            ->createQueryBuilder('r');

        $results = $qb->where($qb->expr()->in(sprintf('r.%s', $field), $oids))
            ->getQuery()
            ->getResult();

        foreach ($results as $result) {
            $hash = $this->buildHashFromResult($metadata, $model, $result, $fields);
            if (array_key_exists($hash, $components)) {
                $components[$hash]->setData($result);
            }
        }
    }

    /**
     * @param ObjectManager $objectManager objectManager
     * @param object        $metadata      metadata
     * @param string        $model         model
     * @param array         $components    components
     * @param array         $oids          oids
     * @param array         $fields        fields
     *
     * @return void
     */
    public function locateComposite(ObjectManager $objectManager, $metadata, $model, array $components, array $oids, array $fields)
    {
        $dqlFields = array_map(function($v) {
            return sprintf('r.%s', $v);
        }, $fields);

        $concat = implode(",'#',", $dqlFields);

        $oids = array_map(function($v) {
            return implode('#', $v); },
        $oids);

        $qb = $objectManager->createQueryBuilder('r');

        $results = $qb->select('r')
            ->from($model, 'r')
            // use string function
            ->where($qb->expr()->in(sprintf("MULTI_CONCAT(%s)", $concat), $oids))
            ->getQuery()
            ->getResult();

        foreach ($results as $result) {
            $hash = $this->buildHashFromResult($metadata, $model, $result, $fields);

            if (array_key_exists($hash, $components)) {
                $components[$hash]->setData($result);
            }
        }
    }

    protected function buildHashFromResult($metadata, $model, $result, array $fields)
    {
        $identifiers = array();
        foreach ($fields as $field) {
            $identifiers[$field] = (string) $metadata->reflFields[$field]->getValue($result);
        }

        if (count($identifiers) == 1) {
            $identifiers = (string) current($identifiers);
        }

        return sprintf('%s#%s', $model, serialize($identifiers));
    }
}
