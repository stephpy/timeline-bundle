<?php

namespace Highco\TimelineBundle\DependencyInjection\Compiler;

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
     * Filters has to be configurated during compil process.
     * Because it must call a fonction "initialize" and use Definition
     * Definition of each filters of application may not be initialized
     * during the execution of TimelineExtension.
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig('highco_timeline');

        if (!isset($config[0]) || !isset($config[0]['filters'])) {

            return;
        }

        $filters = $config[0]['filters'];

        $definition = $container->getDefinition('highco.timeline.manager');
        foreach ($filters as $filter => $arguments) {
            $filter = $container->getDefinition($filter);
            $filter->addMethodCall('initialize', array((array) $arguments['options']));

            $definition->addMethodCall('addFilter', array($filter));
        }
    }
}
