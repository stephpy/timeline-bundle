<?php

namespace Highco\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @uses ConfigurationInterface
 * @package HighcoTimelineBundle
 * @version 1.0.0
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $tb->root('highco_timeline')
            ->children()
                ->arrayNode('filters')
                    ->useAttributeAsKey('options')->prototype('scalar')->end()
                    ->defaultValue(array(
                        'highco.timeline.filter.dupplicate_key',
                        'highco.timeline.filter.data_hydrator',
                    ))
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
                    ->end()
                ->end()
            ->end()
            ;

        return $tb;
    }
}
