<?php

namespace Highco\TimelineBundle\Timeline\Filter;

use Doctrine\Common\Persistence\ObjectManager;
use Highco\TimelineBundle\Timeline\Filter\DataHydrator\Entry;

/**
 * Defined on "Resources/doc/filter.markdown"
 * This filter will hydrate TimelineActions by getting references
 * from Doctrine
 *
 * @uses FilterInterface
 * @package HighcoTimelineBundle
 * @release 1.0.0
 * @author  Stephane PY <py.stephane1@gmail.com>
 */
class DataHydrator implements FilterInterface
{
    /**
     * @var array
     */
    protected $references = array();

    /**
     * @var array
     */
    protected $entries    = array();

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function filter($results)
    {
        foreach ($results as $result) {
            $entry = new Entry($result);
            $entry->build();

            $this->addReferences($entry->getReferences());
            $this->entries[] = $entry;
        }

        $this->hydrateReferences();

        return $results;
    }

    /**
     * Will retrieve references from Doctrine and hydrate entries
     */
    protected function hydrateReferences()
    {
        /* --- Regroup by model --- */
        $referencesByModel = array();
        foreach ($this->references as $reference) {
            if (!array_key_exists($reference->model, $referencesByModel)) {
                $referencesByModel[$reference->model] = array();
            }

            $referencesByModel[$reference->model][$reference->id] = $reference->id;
        }

        /* ---- fetch results from database --- */
        $resultsByModel = array();
        foreach ($referencesByModel as $model => $ids) {
            try {
                $repository = $this->em->getRepository($model);
                if(method_exists($repository, "getTimelineResultsForOIds")) {
                    $results = $repository->getTimelineResultsForOIds($ids);
                } else {
                    $qb = $this->em->createQueryBuilder();

                    $qb
                        ->select('r')
                        ->from($model, 'r INDEX BY r.id')
                        ->where($qb->expr()->in('r.id', $ids))
                        ;

                    $results = $qb->getQuery()->getResult();
                }
            } catch (\Exception $e) {
                $results = array();
            }

            $resultsByModel[$model] = $results;
        }

        /* ---- hydrate references ---- */
        foreach ($this->references as $reference) {
            if (isset($resultsByModel[$reference->model][$reference->id])) {
                $reference->object = $resultsByModel[$reference->model][$reference->id];
            }
        }

        /* ---- hydrate entries ---- */
        foreach ($this->entries as $entry) {
            $entry->hydrate($this->references);
        }
    }

    /**
     * @param array $references
     */
    public function addReferences(array $references)
    {
        $this->references = array_merge($references, $this->references);
    }
}
