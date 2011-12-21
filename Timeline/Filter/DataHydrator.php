<?php

namespace Highco\TimelineBundle\Timeline\Filter;

use Highco\TimelineBundle\Timeline\Filter\DataHydrator\Entry;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * DataHydrator
 *
 * @uses InterfaceFilter
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DataHydrator implements InterfaceFilter
{
    protected $references = array();
    protected $entries    = array();

    private $em;

    /**
     * __construct
     *
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * filter
     *
     * @param array $results
     * @return array
     */
    public function filter($results)
    {
        foreach($results as $result)
        {
            $entry = new Entry($result);
            $entry->build();

            $this->addReferences($entry->getReferences());
            $this->entries[] = $entry;
        }

        $this->hydrateReferences();

        return $results;
    }

    /**
     * hydrateReferences
     */
    protected function hydrateReferences()
    {
        /* --- Regroup by model --- */
        $referencesByModel = array();
        foreach($this->references as $reference)
        {
            if(false === array_key_exists($reference->model, $referencesByModel))
            {
                $referencesByModel[$reference->model] = array();
            }

            $referencesByModel[$reference->model][$reference->id] = $reference->id;
        }

        /* ---- fetch results from database --- */
        $resultsByModel = array();
        foreach($referencesByModel as $model => $ids)
        {
            try {

                $qb = $this->em
                    ->createQueryBuilder()
                    ->select('r')
                    ->from($model, 'r INDEX BY r.id')
                ;

                $results = $qb->where($qb->expr()->in('r.id', $ids))
                    ->getQuery()
                    ->getResult();

            } catch(\Exception $e){
                $results = array();
            }

            $resultsByModel[$model] = $results;
        }

        /* ---- hydrate references ---- */
        foreach($this->references as $reference)
        {
            if(isset($resultsByModel[$reference->model][$reference->id]))
            {
                $reference->object = $resultsByModel[$reference->model][$reference->id];
            }
        }

        /* ---- hydrate entries ---- */
        foreach($this->entries as $entry)
        {
            $entry->hydrate();
        }

    }

    /**
     * addReferences
     *
     * @param array $references
     */
    public function addReferences(array $references)
    {
        $this->references = array_merge($references, $this->references);
    }
}
