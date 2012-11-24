<?php

namespace Spy\TimelineBundle\DependencyInjection;

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

        $tb->root('spy_timeline')
            ->validate()
                ->ifTrue(function($v){return 'orm' === $v['db_driver'] && empty($v['timeline_action_class']);})
                ->thenInvalid('The doctrine model class must be defined by using the "timeline_action_class" key.')
            ->end()
            ->children()
                ->scalarNode('timeline_action_class')->end()
                ->scalarNode('db_driver')->defaultValue('orm')->cannotBeEmpty()->end()
                ->scalarNode('timeline_action_manager')->defaultValue('spy_timeline.timeline_action_manager.default')->end()
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
                            ->ifTrue(function($v){return 'spy_timeline.provider.doctrine.orm' === $v['service'] && empty($v['object_manager']);})
                            ->thenInvalid('The object_manager key must be configured when using the spy_timeline.provider.doctrine.orm provider.')
                        ->end()
                        ->validate()
                            ->ifTrue(function($v){return 'spy_timeline.provider.doctrine.orm' === $v['service'] && empty($v['timeline_class']);})
                            ->thenInvalid('The timeline_class key must be configured when using the spy_timeline.provider.doctrine.orm provider.')
                        ->end()
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('service')->defaultValue('spy_timeline.provider.redis')->end()
                            ->scalarNode('object_manager')->end()
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
                                ->defaultValue(array('SpyTimelineBundle:Action:components.html.twig'))
                                ->validate()
                                    ->ifTrue(function($v) { return !in_array('SpyTimelineBundle:Action:components.html.twig', $v); })
                                    ->then(function($v){
                                        return array_merge(array('SpyTimelineBundle:Action:components.html.twig'), $v);
                                    })
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                ->end();

        return $tb;
    }
}
