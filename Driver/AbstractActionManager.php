<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Spread\Deployer;
use Spy\TimelineBundle\Filter\FilterManager;
use Spy\TimelineBundle\Model\ActionInterface;
use Spy\TimelineBundle\Model\Collection;

/**
 * AbstractActionManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class AbstractActionManager
{
    /**
     * @var Deployer
     */
    protected $deployer;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @param ActionInterface $action action
     *
     * @return void
     */
    protected function deployActionDependOnDelivery(ActionInterface $action)
    {
        if ($this->deployer && $this->deployer->isDeliveryImmediate()) {
            $this->deployer->deploy($action, $this);
        }
    }

    /**
     * @param Deployer $deployer deployer
     */
    public function setDeployer(Deployer $deployer)
    {
        $this->deployer = $deployer;
    }

    /**
     * @param FilterManager $filterManager filterManager
     */
    public function setFilterManager(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @param array $collection collection
     *
     * @return Collection
     */
    public function filterCollection($collection)
    {
        if ($this->filterManager) {
            return $this->filterManager->filter($collection);
        }

        return $collection;
    }
}
