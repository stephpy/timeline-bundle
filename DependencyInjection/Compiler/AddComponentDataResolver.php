<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds the component data resolver to the action manager.
 */
class AddComponentDataResolver implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $actionManager = $container->findDefinition('spy_timeline.action_manager');
        $componentDataResolver = $container->findDefinition('spy_timeline.resolve_component.resolver');

        $actionManager->addMethodCall('setComponentDataResolver', array($componentDataResolver));
    }
}
