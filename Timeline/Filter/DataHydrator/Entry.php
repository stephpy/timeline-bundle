<?php

namespace Highco\TimelineBundle\Timeline\Filter\DataHydrator;

use Symfony\Component\DependencyInjection\Container;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * Entry
 *
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry
{
    private $timeline_action;

    private $references;
    private $referenceRelatedFields = array();

    /**
     * __construct
     *
     * @param TimelineAction $timeline_action
     */
    public function __construct(TimelineAction $timeline_action)
    {
        $this->timeline_action = $timeline_action;
    }

    /**
     * build
     */
    public function build()
    {
        $this->buildReference('subject');
        $this->buildReference('direct_complement');
        $this->buildReference('indirect_complement');
    }

    /**
     * buildReference
     *
     * @param string $name
     */
    public function buildReference($name)
    {
        $key = Container::camelize($name);

        $getSubjectMethod = sprintf('get%s', $key);
        // if object is already setted, we have not to continue
        if(false === is_null($this->timeline_action->{$getSubjectMethod}()))
            return;

        $getModelMethod = sprintf('%sModel', $getSubjectMethod);
        $getIdMethod    = sprintf('%sId', $getSubjectMethod);

        // if model and is are not define, we cannot build reference
        if(is_null($this->timeline_action->{$getModelMethod}()) ||
            is_null($this->timeline_action->{$getIdMethod}()))
            return;

        $reference = new Reference(
            $this->timeline_action->{$getModelMethod}(),
            $this->timeline_action->{$getIdMethod}()
        );

        $refKey = $reference->getKey();
        $this->references[$refKey] = $reference;

        if(false === isset($this->referenceRelatedFields[$refKey]))
        {
            $this->referenceRelatedFields[$refKey] = array();
        }

        $this->referenceRelatedFields[$refKey][] = $name;
    }

    /**
     * hydrate
     *
     * @param array $references
     * @return void
     */
    public function hydrate($references)
    {
        foreach($references as $ref)
        {
            if(false === is_null($ref->object))
            {
                $relatedFields = $this->referenceRelatedFields[$ref->getKey()];
                foreach($relatedFields as $relatedField)
                {
                    $this->hydrateField($relatedField, $ref->object);
                }
            }
        }
    }

    /**
     * hydrateField
     *
     * @param string $name
     * @param object $object
     */
    protected function hydrateField($name, $object)
    {
        $key = Container::camelize($name);

        $setSubjectMethod = sprintf('set%s', $key);
        $this->timeline_action->{$setSubjectMethod}($object);
    }

    /**
     * getReferences
     *
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }
}
