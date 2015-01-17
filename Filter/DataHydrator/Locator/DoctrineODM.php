<?php

namespace Spy\TimelineBundle\Filter\DataHydrator\Locator;

use Spy\Timeline\Filter\DataHydrator\Locator\LocatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineODM implements LocatorInterface
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

            return $objectManager instanceof \Doctrine\ODM\MongoDB\DocumentManager;
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
        $field         = $metadata->getIdentifier();

        $oids = array();
        foreach ($components as $component) {
            $oids[] = $component->getIdentifier();
        }

        $qb    = $objectManager->getRepository($model)
            ->createQueryBuilder('r');
            
        $field = current($field); 
        
        $results = $qb->field($field)->in($oids)
            ->getQuery()
            ->execute();

        foreach ($results as $result) {
            $hash = $this->buildHashFromResult($metadata, $model, $result, $field);
            if (array_key_exists($hash, $components)) {
                $components[$hash]->setData($result);
            }
        }
    }

    protected function buildHashFromResult($metadata, $model, $result, $field)
    {
        $identifiers = array();
        $identifiers[$field] = (string) $metadata->reflFields[$field]->getValue($result);

        if (count($identifiers) == 1) {
            $identifiers = (string) current($identifiers);
        }

        return sprintf('%s#%s', $model, serialize($identifiers));
    }
}
