<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('spy_timeline.resolve_component.doctrine_registries')) {
            return;
        }

        $componentResolver = $container->findDefinition('spy_timeline.resolve_component.resolver');

        foreach (array('doctrine', 'doctrine_mongodb') as $id) {
            if ($container->hasDefinition($id)) {
                $registry = $container->getDefinition($id);
                $componentResolver->addMethodCall('addRegistry', array($registry));
            }
        }
    }
}
