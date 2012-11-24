<?php

namespace Spy\TimelineBundle\Filter\DataHydrator;

use Symfony\Component\DependencyInjection\Container;
use Spy\TimelineBundle\Model\TimelineAction;

/**
 * Entry, each timeline actions are an entry
 *
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
    private $references = array();

    /**
     * @var array
     */
    private $resolvedReferences = array();

    /**
     * @var array
     */
    private $referenceRelatedFields = array();

    /**
     * @var int
     */
    protected $key;

    /**
     * @param TimelineAction $timelineAction
     */
    public function __construct(TimelineAction $timelineAction, $key)
    {
        $this->timelineAction = $timelineAction;
        $this->key = $key;
    }

    /**
     * Build references (subject, directComplement, indirectComplement)
     * of timeline action
     */
    public function build()
    {
        $this->buildReference('subject');
        $this->buildReference('directComplement');
        $this->buildReference('indirectComplement');
    }

    /**
     * @param string $name
     */
    public function buildReference($name)
    {
        $getSubjectMethod = sprintf('get%s', Container::camelize($name));

        // if object is already set (and not an non-inited proxy), we don't need to continue
        $object = $this->timelineAction->{$getSubjectMethod}();
        if (null !== $object
            AND (!$object instanceof \Doctrine\Common\Persistence\Proxy OR $object->__isInitialized())
        ) {
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
        $this->resolvedReferences[$refKey] = false;

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
                $this->resolvedReferences[$key] = true;
                foreach ($fields as $field) {
                    $this->hydrateField($field, $references[$key]->object);
                }
            }
        }
    }

    public function isFullyResolved() {
        foreach($this->resolvedReferences as $resolved) {
            if(!$resolved) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $name   The name of field to hydrate
     * @param object $object The object which is the value
     */
    protected function hydrateField($name, $object)
    {
        $setSubjectMethod = sprintf('set%s', Container::camelize($name));
        $this->timelineAction->{$setSubjectMethod}($object);
    }

    /**
     * @return array<*,Reference>
     */
    public function getReferences()
    {
        return $this->references;
    }

    public function getKey()
    {
        return $this->key;
    }
}
