<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * AddFilterCompilerPass
 *
 * @uses CompilerPassInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
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

        //add the locators to the data hydrator.
        $locators = $container->getParameter('spy_timeline.filter.data_hydrator.locators');
        //locators are not set...
        if (empty($locators) || !is_array($locators)) {
            return;
        }

        $dataHydrator = $container->getDefinition('spy_timeline.filter.data_hydrator');
        foreach ($locators as $locator) {
            $dataHydrator->addMethodCall('addLocator', array($container->getDefinition($locator)));
        }
    }
}
