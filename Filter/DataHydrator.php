<?php

namespace Spy\TimelineBundle\Filter;

use Spy\TimelineBundle\Model\Collection;
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
    public function filter(Collection $collection)
    {
        if (empty($this->locators)) {
            return $collection;
        }

        foreach ($collection as $key => $result) {
            $entry = new Entry($result, $key);
            $entry->build();

            $this->addComponents($entry->getComponents());
        }

        $this->hydrateComponents();

        if ($this->filterUnresolved) {
            foreach ($collection as $key => $action) {
                if (!$action->hasComponentHydrated()) {
                    unset($collection[$key]);
                }
            }
        }

        return $collection;
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
    public function hydrateComponents()
    {
        foreach ($this->components as $model => $components) {
            foreach ($this->locators as $locator) {
                if ($locator->supports($model)) {
                    $locator->locate($model, $components);
                    break;
                }
            }
        }
    }
}
