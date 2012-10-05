<?php

namespace Highco\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @uses ConfigurationInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Chris Jones <leeked@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('highco_timeline');

        $supportedDrivers = array('orm', 'mongodb');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode($supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('timeline_action_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('timeline_action_manager')->defaultValue('highco.timeline_action_manager.default')->end()
                ->arrayNode('notifiers')
                    ->useAttributeAsKey('options')->prototype('scalar')->end()
                    ->defaultValue(array())
                ->end()
                ->arrayNode('filters')
                    ->useAttributeAsKey('filters')
                        ->prototype('array')
                            ->children()
                                ->arrayNode('options')
                                    ->useAttributeAsKey('options')
                                    ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->children()
                    ->arrayNode('spread')
                        ->children()
                            ->scalarNode('on_me')->defaultValue(true)->end()
                            ->scalarNode('on_global_context')->defaultValue(true)->end()
                        ->end()
                    ->end()
                ->end()
                ->children()
                    ->scalarNode('provider')->defaultValue('highco.timeline.provider.redis')->end()
                    ->scalarNode('entity_retriever')->defaultValue('highco.timeline.provider.doctrine.dbal')->end()
                ->end()
                ->children()
                    ->scalarNode('delivery')->defaultValue('immediate')->end()
                ->end()
                ->children()
                    ->arrayNode('render')
                        ->isRequired()
                        ->children()
                            ->scalarNode('path')->isRequired()->end()
                            ->scalarNode('fallback')->defaultValue(null)->end()
                            ->arrayNode('i18n')
                                ->children()
                                    ->scalarNode('fallback')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
