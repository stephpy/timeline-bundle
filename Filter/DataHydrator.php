<?php

namespace Spy\TimelineBundle\Filter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Spy\TimelineBundle\Filter\DataHydrator\Entry;

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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     * @param array $options
     */
    public function initialize(array $options = array())
    {
        $defaultOptions = array(
            'db_driver' => 'orm',
            'filter_unresolved' => false,
        );

        $this->setOptions(array_merge($defaultOptions, $options));
    }

    /**
     * {@inheritdoc}
     * @param \Spy\TimelineBundle\Model\Collection $results
     */
    public function filter($results)
    {
        foreach ($results as $key => $result) {
            $entry = new Entry($result, $key);
            $entry->build();

            $this->addReferences($entry->getReferences());
            $this->entries[] = $entry;
        }

        $this->hydrateReferences();

        if($this->getOption('filter_unresolved')) {
            $results = $this->removeUnresolved($results);
        }

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
     * Remove any results which have unresolved references
     * @param $results
     *
     * @return mixed
     */
    public function removeUnresolved($results) {
        foreach ($this->entries as $entry) {
            if(!$entry->isFullyResolved()) {
                unset($results[$entry->getKey()]);
            }
        }
        return $results;
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
        $dbDriver = $this->getOption('db_driver', 'orm');

        switch ($dbDriver) {
            case 'orm':
                $objectManager = $this->container->get('spy_timeline.entity_manager');
                $repository    = $objectManager->getRepository($model);

                if (method_exists($repository, "getTimelineResultsForModelAndOids")) {
                    return $repository->getTimelineResultsForModelAndOids($oids);
                } else {
                    $qb = $objectManager->createQueryBuilder();

                    $qb
                        ->select('r')
                        ->from($model, 'r INDEX BY r.id')
                        ->where($qb->expr()->in('r.id', $oids));

                    return $qb->getQuery()->getResult();
                }

                break;
            default;
                throw new \OutOfRangeException(sprintf('%s is not accepted by DataHydrator', $dbDriver));
                break;
        }
    }
}
