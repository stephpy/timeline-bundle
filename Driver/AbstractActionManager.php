<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Spread\Deployer;
use Spy\TimelineBundle\Pager\PagerInterface;
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
     * @var PagerInterface
     */
    protected $pager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * {@inheritdoc}
     */
    public function create($subject, $verb, array $components = array())
    {
        $action = new $this->actionClass();
        $action->setVerb($verb);

        // subject is MANDATORY. Cannot pass scalar value.
        if (!$subject instanceof ComponentInterface) {
            if (is_object($subject)) {
                $subject = $this->findOrCreateComponent($subject);
            }

            if (null === $subject) {
                throw new \Exception('Impossible to create component from subject.');
            }
        }

        $action->setSubject($subject, $this->actionComponentClass);

        foreach ($components as $type => $component) {
            if (!$component instanceof ComponentInterface && !is_scalar($component)) {
                $component = $this->findOrCreateComponent($component);

                if (null === $component) {
                    throw new \Exception(sprintf('Impossible to create component from %s.', $type));
                }
            }

            $action->addComponent($type, $component, $this->actionComponentClass);
        }

        return $action;
    }

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
     * @param PagerInterface $pager pager
     */
    public function setPager(PagerInterface $pager)
    {
        $this->pager = $pager;
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
