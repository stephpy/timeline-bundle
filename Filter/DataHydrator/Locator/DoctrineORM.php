<?php

namespace Spy\TimelineBundle\Filter\DataHydrator\Locator;

use Doctrine\ORM\QueryBuilder;
use Spy\Timeline\Filter\DataHydrator\Locator\LocatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

class DoctrineORM implements LocatorInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry registry
     */
    public function __construct(ManagerRegistry $registry = null)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model)
    {
        if (null === $this->registry) {
            return false;
        }

        if (strpos($model, '\\') === 0) {
            $model = substr($model, 1);
        }

        try {
            $objectManager = $this->registry->getManagerForClass($model);

            return $objectManager instanceof \Doctrine\ORM\EntityManager;
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

        $oids = array();
        foreach ($components as $component) {
            $oids[] = $component->getIdentifier();
        }

        if (count($fields) > 1) {
            return $this->locateComposite($objectManager, $metadata, $model, $components, $oids, $fields);
        }

        $alias = 'r';
        $field = current($fields);
        $qb    = $objectManager->getRepository($model)
            ->createQueryBuilder($alias)
        ;

        $this->onPreLocate($qb, $alias, $model, $oids);

        $results = $qb->where($qb->expr()->in(sprintf('%s.%s', $alias, $field), $oids))
            ->getQuery()
            ->getResult()
        ;

        foreach ($results as $result) {
            $hash = $this->buildHashFromResult($metadata, $model, $result, $fields);
            if (array_key_exists($hash, $components)) {
                $components[$hash]->setData($result);
            }
        }
    }

    /**
     * Modify the locating query
     *
     * @param QueryBuilder $qb
     * @param $alias
     * @param $model
     * @param array $oids
     */
    protected function onPreLocate(QueryBuilder $qb, $alias, $model, array $oids)
    { }

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
        $alias = 'r';
        $dqlFields = array_map(function($alias, $v) {
            return sprintf('%s.%s', $alias, $v);
        }, $fields);

        $concat = implode(",'#',", $dqlFields);

        $oids = array_map(function($v) {
            return implode('#', $v); },
        $oids);

        $qb = $objectManager->createQueryBuilder($alias);

        $this->onPreLocateComposite($qb, $alias, $model, $oids);

        $results = $qb->select($alias)
            ->from($model, $alias)
            // use string function
            ->where($qb->expr()->in(sprintf("MULTI_CONCAT(%s)", $concat), $oids))
            ->getQuery()
            ->getResult()
        ;

        foreach ($results as $result) {
            $hash = $this->buildHashFromResult($metadata, $model, $result, $fields);

            if (array_key_exists($hash, $components)) {
                $components[$hash]->setData($result);
            }
        }
    }

    /**
     * Modify the doctrine query on locating composites
     *
     * @param QueryBuilder $qb
     * @param $alias
     * @param $model
     * @param array $oids
     */
    protected function onPreLocateComposite(QueryBuilder $qb, $alias, $model, array $oids)
    { }


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
