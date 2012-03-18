<?php

namespace Highco\TimelineBundle\Timeline\Filter;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Timeline\Filter\DataHydrator\Entry;

/**
 * Defined on "Resources/doc/filter.markdown"
 * This filter will hydrate TimelineActions by getting references
 * from Doctrine
 *
 * @uses FilterInterface
 * @author Stephane PY <py.stephane1@gmail.com>
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
     * @var TimelineActionManagerInterface
     */
    private $timelineActionManager;

    /**
     * @param TimelineActionManagerInterface $timelineActionManager
     */
    public function __construct(TimelineActionManagerInterface $timelineActionManager)
    {
        $this->timelineActionManager = $timelineActionManager;
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
                $results = $this->timelineActionManager->getTimelineResultsForModelAndOids($model, $ids);
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
