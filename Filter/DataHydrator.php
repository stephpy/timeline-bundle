<?php

namespace Highco\TimelineBundle\Filter;

use Highco\TimelineBundle\Model\TimelineActionManagerInterface;
use Highco\TimelineBundle\Filter\DataHydrator\Entry;

/**
 * Defined on "Resources/doc/filter.markdown"
 * This filter will hydrate TimelineActions by getting references
 * from Doctrine
 *
 * @uses AbstractFilter
 * @uses FilterInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class DataHydrator extends AbstractFilter implements FilterInterface
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
     * @param TimelineActionManagerInterface $timelineActionManager TimelineActionManager
     */
    public function __construct(TimelineActionManagerInterface $timelineActionManager)
    {
        $this->timelineActionManager = $timelineActionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array $options = array())
    {
        $defaultOptions = array(
            'db_driver' => $this->dbDriver,
        );

        $this->setOptions(array_merge($defaultOptions, $options));
    }

    /**
     * {@inheritdoc}
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
            $resultsByModel[$model] = $this->_getTimelineResultsForModelAndOids($model, (array) $ids);
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

    /**
     * Return timeline results from storage (actually only 'orm')
     *
     * @param string $model Model to retrieve
     * @param array  $oids  An array of oids
     *
     * @return array
     */
    protected function _getTimelineResultsForModelAndOids($model, array $oids)
    {
        switch ($this->getOption('db_driver')) {
            case 'orm':
                $em         = $this->timelineActionManager->getEntityManager();
                $repository = $em->getRepository($model);

                if (method_exists($repository, "getTimelineResultsForModelAndOids")) {
                    return $repository->getTimelineResultsForModelAndOids($oids);
                } else {
                    $qb = $em->createQueryBuilder();

                    $qb
                        ->select('r')
                        ->from($model, 'r INDEX BY r.id')
                        ->where($qb->expr()->in('r.id', $oids));

                    return $qb->getQuery()->getResult();
                }

                break;
            default;
                throw new \OutOfRangeException(sprintf('%s is not accepted by DataHydrator', $this->dbDriver));
                break;
        }
    }
}
