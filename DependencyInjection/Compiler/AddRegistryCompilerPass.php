<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * AddRegistryCompilerPass
 *
 * @uses CompilerPassInterface
 * @author Emmanuel Vella <vella.emmanuel@gmail.com>
 */
class AddRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (array('orm', 'odm') as $driver) {
            $id = sprintf('spy_timeline.action_manager.%s', $driver);

            if ($container->hasDefinition($id)) {
                $actionManager = $container->getDefinition($id);

                foreach (array('doctrine', 'doctrine_mongodb') as $id) {
                    if ($container->hasDefinition($id)) {
                        $registry = $container->getDefinition($id);
                        $actionManager->addMethodCall('addRegistry', array($registry));
                    }
                }
            }
        }
    }
}
