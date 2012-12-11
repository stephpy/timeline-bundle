<?php

namespace Spy\TimelineBundle\Filter;

use Spy\TimelineBundle\Filter\DataHydrator\Locator\LocatorInterface;
use Spy\TimelineBundle\Filter\DataHydrator\Entry;
use Spy\TimelineBundle\Filter\DataHydrator\Reference;

/**
 * DataHydrator
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
    protected $locators = array();

    /**
     * @var array
     */
    protected $components = array();

    /**
     * @var array
     */
    protected $entries = array();

    /**
     * @var boolean
     */
    protected $filterUnresolved;

    /**
     * @param boolean $filterUnresolved filterUnresolved
     */
    public function __construct($filterUnresolved = false)
    {
        $this->filterUnresolved = $filterUnresolved;
    }

    /**
     * @param LocatorInterface $locator locator
     */
    public function addLocator(LocatorInterface $locator)
    {
        $this->locators[] = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($collection)
    {
        if (empty($this->locators)) {
            return $collection;
        }

        foreach ($collection as $key => $result) {
            $entry = new Entry($result, $key);
            $entry->build();

            $this->addComponents($entry->getComponents());
        }

        return $this->hydrateComponents($collection);
    }

    /**
     * @param array $components
     */
    public function addComponents(array $components)
    {
        foreach ($components as $component) {
            $model = $component->getModel();
            if (!array_key_exists($model, $this->components)) {
                $this->components[$model] = array();
            }

            $this->components[$model][$component->getHash()] = $component;
        }
    }

    /**
     * Use locators to hydrate components.
     */
    public function hydrateComponents($collection)
    {
        $componentsLocated = array();

        foreach ($this->components as $model => $components) {
            foreach ($this->locators as $locator) {
                if ($locator->supports($model)) {
                    $locator->locate($model, $components);

                    foreach ($components as $key => $component) {
                        $componentsLocated[$key] = $component;
                    }

                    break;
                }
            }
        }

        foreach ($collection as $key => $action) {
            foreach ($action->getActionComponents() as $actionComponent) {
                if (!$actionComponent->isText() && null === $actionComponent->getComponent()->getData()) {
                    $hash = $actionComponent->getComponent()->getHash();

                    if (array_key_exists($hash, $componentsLocated) && !empty($componentsLocated[$hash])) {
                        $actionComponent->setComponent($componentsLocated[$hash]);
                    } else {
                        if ($this->filterUnresolved) {
                            unset($collection[$key]);
                            break;
                        }
                    }
                }
            }
        }

        return $collection;
    }
}
