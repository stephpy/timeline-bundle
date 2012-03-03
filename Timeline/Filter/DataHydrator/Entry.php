<?php

namespace Highco\TimelineBundle\Timeline\Filter\DataHydrator;

use Symfony\Component\DependencyInjection\Container;
use Highco\TimelineBundle\Model\TimelineAction;

/**
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry
{
    /**
     * @var TimelineAction
     */
    private $timelineAction;

    /**
     * @var array
     */
    private $references;

    /**
     * @var array
     */
    private $referenceRelatedFields = array();

    /**
     * @param TimelineAction $timelineAction
     */
    public function __construct(TimelineAction $timelineAction)
    {
        $this->timelineAction = $timelineAction;
    }

    /**
     *
     */
    public function build()
    {
        $this->buildReference('subject');
        $this->buildReference('direct_complement');
        $this->buildReference('indirect_complement');
    }

    /**
     * @param string $name
     */
    public function buildReference($name)
    {
        $key = Container::camelize($name);

        $getSubjectMethod = sprintf('get%s', $key);
        // if object is already setted, we have not to continue
        if (null !== $this->timelineAction->{$getSubjectMethod}()) {
            return;
        }

        $getModelMethod = sprintf('%sModel', $getSubjectMethod);
        $getIdMethod    = sprintf('%sId', $getSubjectMethod);

        // if model and is are not define, we cannot build reference
        if (null === $this->timelineAction->{$getModelMethod}()
            || null === $this->timelineAction->{$getIdMethod}()) {
            return;
        }

        $reference = new Reference(
            $this->timelineAction->{$getModelMethod}(),
            $this->timelineAction->{$getIdMethod}()
        );

        $refKey = $reference->getKey();
        $this->references[$refKey] = $reference;

        if (!isset($this->referenceRelatedFields[$refKey])) {
            $this->referenceRelatedFields[$refKey] = array();
        }

        $this->referenceRelatedFields[$refKey][] = $name;
    }

    /**
     * @param array $references
     */
    public function hydrate($references)
    {
        foreach ($this->referenceRelatedFields as $key => $fields) {
            if (array_key_exists($key, $references) && null !== $references[$key]->object) {
                foreach ($fields as $field) {
                    $this->hydrateField($field, $references[$key]->object);
                }
            }
        }
    }

    /**
     * @param string $name
     * @param object $object
     */
    protected function hydrateField($name, $object)
    {
        $key = Container::camelize($name);

        $setSubjectMethod = sprintf('set%s', $key);
        $this->timelineAction->{$setSubjectMethod}($object);
    }

    /**
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }
}
