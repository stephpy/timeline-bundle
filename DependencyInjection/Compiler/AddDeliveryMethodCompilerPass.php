<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddDeliveryMethodCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // we only do a call if the delivery method is immediate.
        if ($container->getParameter('spy_timeline.spread.deployer.delivery') !== "immediate") {
            return;
        }

        $actionManager = $container->findDefinition('spy_timeline.action_manager');
        $deployer = $container->findDefinition('spy_timeline.spread.deployer');
        $actionManager->addMethodCall('setDeployer', array($deployer));
    }
}
