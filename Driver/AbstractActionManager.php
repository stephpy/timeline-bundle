<?php

namespace Spy\TimelineBundle\Driver;

use Spy\TimelineBundle\Spread\Deployer;
use Spy\TimelineBundle\Model\ActionInterface;

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
     * @param ActionInterface $action action
     *
     * @return void
     */
    protected function deployActionDependOnDelivery(ActionInterface $action)
    {
        if ($this->deployer && $this->deployer->isDeliveryImmediate()) {
            $this->deployer->deploy($action);
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
