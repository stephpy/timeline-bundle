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
        $spreadManager = $container->getDefinition('spy_timeline.spread.manager');
        foreach ($container->findTaggedServiceIds('spy_timeline.spread') as $id => $tags) {
            $spreadManager->addMethodCall('add', array($container->getDefinition($id)));
        }
    }
}
