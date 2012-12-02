<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * AddSpreadCompilerPass
 *
 * @uses CompilerPassInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class AddSpreadCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $alias          = $container->getAlias('spy_timeline.spread.deployer');
        $spreadDeployer = $container->getDefinition((string) $alias);

        foreach ($container->findTaggedServiceIds('spy_timeline.spread') as $id => $tags) {
            $spreadDeployer->addMethodCall('addSpread', array($container->getDefinition($id)));
        }
    }
}
