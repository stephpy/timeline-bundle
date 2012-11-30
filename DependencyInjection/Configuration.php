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
        $tb       = new TreeBuilder();
        $rootNode = $tb->root('spy_timeline');

        $rootNode
            ->validate()
                ->ifTrue(function($v) {
                    if (count($v['drivers']) == 0) {
                        return !isset($v['timeline_manager']) || !isset($v['action_manager']);
                    }

                    return false;
                })
                ->thenInvalid("Please define a driver or timeline_manager, action_manager")
            ->end()
            ->children()
                ->arrayNode('drivers')
                    ->validate()
                        ->ifTrue(function($v) {
                            return count($v) > 1;
                        })
                        ->thenInvalid('Please define only one driver.')
                    ->end()
                    ->children()
                        ->arrayNode('orm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->defaultValue('doctrine.orm.entity_manager')
                                ->end()
                                ->arrayNode('classes')
                                    ->children()
                                        ->scalarNode('timeline')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Entity\Timeline')
                                        ->end()
                                        ->scalarNode('action')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Entity\Action')
                                        ->end()
                                        ->scalarNode('component')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Entity\Component')
                                        ->end()
                                        ->scalarNode('action_component')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Entity\ActionComponent')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('odm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->defaultValue('doctrine.odm.entity_manager')
                                ->end()
                                ->arrayNode('classes')
                                    ->children()
                                        ->scalarNode('timeline')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Document\Timeline')
                                        ->end()
                                        ->scalarNode('action')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Document\Action')
                                        ->end()
                                        ->scalarNode('component')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Document\Component')
                                        ->end()
                                        ->scalarNode('action_component')
                                            ->isRequired()
                                            ->cannotBeEmpty()
                                            ->example('Acme\YourBundle\Document\ActionComponent')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('redis')
                            ->children()
                                ->scalarNode('client')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->example('snc_redis.default')
                                ->end()
                                ->booleanNode('pipeline')
                                    ->defaultTrue()
                                ->end()
                                ->scalarNode('timeline_key_prefix')
                                    ->defaultValue('timeline:')
                                ->end()
                                ->scalarNode('action_key_prefix')
                                    ->defaultValue('timeline:action')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->scalarNode('timeline_manager')
                    ->info('Do not define it if you use a core driver.')
                ->end()
                ->scalarNode('action_manager')
                    ->info('Do not define it if you use a core driver.')
                ->end()
            ->end()
            ->children()
                ->arrayNode('notifiers')
                    ->prototype('scalar')
                    ->end()
                ->end()
            ->end()
            ->children()
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('on_subject')->defaultValue(true)->end()
                        ->booleanNode('on_global_context')->defaultValue(true)->end()
                        ->scalarNode('deployer')->defaultValue('spy_timeline.spread.deployer')->end()
                        ->scalarNode('delivery')->defaultValue('immediate')->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('render')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('resource')
                    ->children()
                        ->scalarNode('path')->defaultValue('SpyTimelineBundle:Timeline')->end()
                        ->scalarNode('fallback')->defaultValue('SpyTimelineBundle:Timeline:default.html.twig')->end()
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
                ->end()
            ->end()
        ->end();

        return $tb;
    }

}
