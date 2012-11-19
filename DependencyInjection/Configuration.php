<?php

namespace Highco\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @uses ConfigurationInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $tb->root('highco_timeline')
            ->validate()
                ->ifTrue(function($v){return 'orm' === $v['db_driver'] && empty($v['timeline_action_class']);})
                ->thenInvalid('The doctrine model class must be defined by using the "timeline_action_class" key.')
            ->end()
            ->children()
                ->scalarNode('timeline_action_class')->end()
                ->scalarNode('db_driver')->defaultValue('orm')->cannotBeEmpty()->end()
                ->scalarNode('timeline_action_manager')->defaultValue('highco.timeline_action_manager.default')->end()
                ->arrayNode('notifiers')
                    ->useAttributeAsKey('options')->prototype('scalar')->end()
                    ->defaultValue(array(
                    ))
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
                    ->arrayNode('provider')
                        ->validate()
                            ->ifTrue(function($v) {
                                return empty($v['type']) && empty($v['service']);
                            })
                            ->thenInvalid('You have to define a service or a type on provider node.')
                        ->end()
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) { return array('service' => $v); })
                        ->end()
                        ->validate()
                            ->ifTrue(function($v){return isset($v['type']) && 'orm' === $v['type'] && empty($v['timeline_class']);})
                            ->thenInvalid('timeline_class must be configured when using the ORM provider, look at documentation.')
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('service')
                                ->validate()
                                    ->ifTrue(function($v) {
                                        return empty($v);
                                    })
                                    ->thenUnset()
                                ->end()
                            ->end()
                            ->scalarNode('type')
                                ->validate()
                                    ->ifNotInArray(array('orm', 'redis'))
                                    ->thenInvalid('Unknown provider type %s.')
                                ->end()
                                ->validate()
                                    ->ifTrue(function($v) {
                                        return empty($v);
                                    })
                                    ->thenUnset()
                                ->end()
                            ->end()
                            ->scalarNode('object_manager')->defaultValue('doctrine.orm.entity_manager')->end()
                            ->scalarNode('timeline_class')->end()
                        ->end()
                    ->end()
                ->end()
                ->children()
                    ->scalarNode('delivery')->defaultValue('immediate')->end()
                ->end()
                ->children()
                    ->arrayNode('render')
                        ->addDefaultsIfNotSet()
                        ->fixXmlConfig('resource')
                        ->isRequired()
                        ->children()
                            ->scalarNode('path')->isRequired()->end()
                            ->scalarNode('fallback')->defaultValue(null)->end()
                            ->arrayNode('i18n')
                                ->children()
                                    ->scalarNode('fallback')->isRequired()->end()
                                ->end()
                            ->end()
                            ->arrayNode('resources')
                                ->defaultValue(array('HighcoTimelineBundle:Action:components.html.twig'))
                                ->validate()
                                    ->ifTrue(function($v) { return !in_array('HighcoTimelineBundle:Action:components.html.twig', $v); })
                                    ->then(function($v){
                                        return array_merge(array('HighcoTimelineBundle:Action:components.html.twig'), $v);
                                    })
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                ->end();

        return $tb;
    }
}
