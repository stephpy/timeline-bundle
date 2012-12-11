<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Spread\Deployer;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;

/**
 * AbstractActionManager
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
abstract class AbstractActionManager
{
    /**
     * @var Deployer
     */
    protected $deployer;

    /**
     * {@inheritdoc}
     */
    public function create($subject, $verb, array $components = array())
    {
        $action = new $this->actionClass();
        $action->setVerb($verb);

        if(!$subject instanceof ComponentInterface AND !is_object($subject)) {
            throw new \Exception('Subject must be a ComponentInterface or an object');
        }

        $components['subject'] = $subject;

        foreach ($components as $type => $component) {
            $this->addComponent($action, $type, $component);
        }

        return $action;
    }

    /**
     * @param ActionInterface $action    action
     * @param string          $type      type
     * @param mixed           $component component
     */
    public function addComponent($action, $type, $component)
    {
        if (!$component instanceof ComponentInterface && !is_scalar($component)) {
            $component = $this->findOrCreateComponent($component);

            if (null === $component) {
                throw new \Exception(sprintf('Impossible to create component from %s.', $type));
            }
        }

        $action->addComponent($type, $component, $this->actionComponentClass);
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
}
