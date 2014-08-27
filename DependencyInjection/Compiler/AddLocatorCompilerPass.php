<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds a method call AddLocator to the hydrator service for the registered locator services.
 *
 * This compiler pass makes it possible to define locators in the config and as tagged services.
 * See https://github.com/stephpy/TimelineBundle/issues/125
 */
class AddLocatorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $parameterDefinitions = $this->getLocatorDefinitionsFromParameter($container);
        $taggedServiceDefinitions = $this->getLocatorDefinitionsFromTaggedServices($container);
        $uniqueLocatorDefinitions = array_merge($parameterDefinitions, $taggedServiceDefinitions);

        if (empty($uniqueLocatorDefinitions)) {
            return;
        }

        $dataHydrator = $container->getDefinition('spy_timeline.filter.data_hydrator');
        foreach ($uniqueLocatorDefinitions as $definition) {
            $dataHydrator->addMethodCall('addLocator', array($definition));
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getLocatorDefinitionsFromParameter(ContainerBuilder $container)
    {
        $data = array();
        $locatorServiceIdArray = array();

        //get the config array from the parameter.
        if ($container->hasParameter('spy_timeline.filter.data_hydrator.locators_config')) {
            $locatorServiceIdArray = $container->getParameter('spy_timeline.filter.data_hydrator.locators_config');
        }

        if ($this->validConfigLocators($locatorServiceIdArray)) {
            foreach ($locatorServiceIdArray as $serviceId) {
                $data[$serviceId] = $container->getDefinition($serviceId);
            }
        }

        return $data;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getLocatorDefinitionsFromTaggedServices(ContainerBuilder $container)
    {
        $data = array();
        //get the locatorServiceIdArray from the tagged services
        foreach ($container->findTaggedServiceIds('spy_timeline.filter.data_hydrator.locator') as $serviceId => $tags) {
            $data[$serviceId] = $container->getDefinition($serviceId);
        }

        return $data;
    }

    /**
     * The config locatorServiceIdArray parameter should contain an array with locatorServiceIdArray.
     *
     * @param array $locatorServiceIdArray
     *
     * @return boolean
     */
    private function validConfigLocators($locatorServiceIdArray)
    {
        return !empty($locatorServiceIdArray) && is_array($locatorServiceIdArray);
    }
}
