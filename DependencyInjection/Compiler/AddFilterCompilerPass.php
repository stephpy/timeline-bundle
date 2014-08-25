<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddFilterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filterManager = $container->getDefinition('spy_timeline.filter.manager');

        foreach ($container->findTaggedServiceIds('spy_timeline.filter') as $id => $tags) {
            $filterManager->addMethodCall('add', array($container->getDefinition($id)));
        }
    }
}
