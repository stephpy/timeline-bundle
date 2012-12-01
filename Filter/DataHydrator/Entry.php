<?php

namespace Spy\TimelineBundle\Filter\DataHydrator;

use Symfony\Component\DependencyInjection\Container;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\ActionComponentInterface;

/**
 * Entry, each timeline actions are an entry
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Entry
{
    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var array
     */
    private $components = array();

    /**
     * @var int
     */
    protected $key;

    /**
     * @param ActionInterface $action action
     * @param string          $key    key
     */
    public function __construct(ActionInterface $action, $key)
    {
        $this->action = $action;
        $this->key    = $key;
    }

    /**
     * Build references (subject, directComplement, indirectComplement)
     * of timeline action
     */
    public function build()
    {
        foreach ($this->action->getActionComponents() as $actionComponent) {
            if (!$actionComponent->isText()) {
                $this->buildComponent($actionComponent);
            }
        }
    }

    /**
     * @param ActionComponentInterface $actionComponent actionComponent
     */
    public function buildComponent(ActionComponentInterface $actionComponent)
    {
        $component = $actionComponent->getComponent();
        $data      = $component->getData();

        if (null !== $data
            && (!$data instanceof \Doctrine\Common\Persistence\Proxy || $data->__isInitialized())
        ) {
            return;
        }

        $this->components[$component->getHash()] = $component;
    }

    /**
     * @param array $references
     */
    public function hydrate($references)
    {
        exit('HYDRATE entry');
        foreach ($this->referenceRelatedFields as $key => $fields) {
            if (array_key_exists($key, $references) && null !== $references[$key]->data) {
                $this->resolvedReferences[$key] = true;
                foreach ($fields as $field) {
                    $this->hydrateField($field, $references[$key]->data);
                }
            }
        }
    }

    public function isFullyResolved()
    {
        exit('IS FULLY RESOLVED');
        foreach ($this->resolvedReferences as $resolved) {
            if (!$resolved) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $name The name of field to hydrate
     * @param object $data The object which is the value
     */
    protected function hydrateField($name, $data)
    {
        exit('HYDRATE FIELD');
        $setSubjectMethod = sprintf('set%s', Container::camelize($name));
        $this->timelineAction->{$setSubjectMethod}($object);
    }

    /**
     * @return array<*,Reference>
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
