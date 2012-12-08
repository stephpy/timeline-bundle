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
     * @return array<*,Reference>
     */
    public function getComponents()
    {
        return $this->components;
    }
}
