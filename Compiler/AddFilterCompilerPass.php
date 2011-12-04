<?php

namespace Highco\TimelineBundle\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddFilterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach($container->findTaggedServiceIds('highco.timeline.filter') as $id => $attributes)
        {
			$priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 255;
            $container->getDefinition('highco.timeline.local.puller')->addMethodCall('addFilter', array(new Reference($id), $priority));
        }
    }
}
