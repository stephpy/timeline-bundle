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
            ->children()
                ->arrayNode('classes')
                    ->children()
                        ->scalarNode('timeline')->example('Acme\YourBundle\Entity\Timeline')->end()
                        ->scalarNode('action')->example('Acme\YourBundle\Entity\Action')->end()
                        ->scalarNode('component')->example('Acme\YourBundle\Entity\Component')->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('drivers')
                    ->children()
                        ->arrayNode('orm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->defaultValue('doctrine.orm.entity_manager')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('odm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->defaultValue('doctrine.odm.entity_manager')
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
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->scalarNode('timeline_manager')
                    ->beforeNormalization()
                        ->ifInArray(array('orm', 'odm','redis'))
                        ->then(function ($v) { return sprintf('spy_timeline.timeline_manager.%s', $v); })
                    ->end()
                    ->example('orm')
                ->end()
                ->scalarNode('action_manager')
                    ->beforeNormalization()
                        ->ifInArray(array('orm', 'odm','redis'))
                        ->then(function ($v) { return sprintf('spy_timeline.action_manager.%s', $v); })
                    ->end()
                    ->example('orm')
                ->end()
                ->scalarNode('component_manager')
                    ->beforeNormalization()
                        ->ifInArray(array('orm', 'odm','redis'))
                        ->then(function ($v) { return sprintf('spy_timeline.component_manager.%s', $v); })
                    ->end()
                    ->example('orm')
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
                    ->children()
                        ->scalarNode('on_subject')->defaultValue(true)->end()
                        ->scalarNode('on_global_context')->defaultValue(true)->end()
                        ->scalarNode('deployer')->defaultValue('spy_timeline.spread.deployer')->end()
                        ->scalarNode('delivery')->defaultValue('immediate')->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('render')
                    ->fixXmlConfig('resource')
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
                ->end()
            ->end()
        ->end();

        $this->addDriverValidation($rootNode);

        return $tb;
    }

    protected function addDriverValidation($treeBuilder)
    {
        $hasDriverRequest = function($v, $driver) {
            $timelineManager  = sprintf('spy_timeline.timeline_manager.%s', $driver);
            $actionManager    = sprintf('spy_timeline.action_manager.%s', $driver);
            $componentManager = sprintf('spy_timeline.component_manager.%s', $driver);

            if ((isset($v['timeline_manager']) && $v['timeline_manager'] == $timelineManager) ||
                (isset($v['action_manager']) && $v['action_manager'] == $actionManager) ||
                (isset($v['component_manager']) && $v['component_manager'] == $componentManager)) {
                return false;
            }

            return true;
        };

        $validateDriver = function($v, $driver) use ($hasDriverRequest){
            if (isset($v['drivers']) && isset($v['drivers'][$driver])) {
                return true;
            }

            return $hasDriverRequest($v, $driver);
        };

        $treeBuilder
            ->validate()
                ->ifTrue(function ($v) use ($validateDriver) { return !$validateDriver($v, 'orm'); })->thenInvalid('You have to define driver "orm"')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateDriver) { return !$validateDriver($v, 'odm'); })->thenInvalid('You have to define driver "odm"')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateDriver) { return !$validateDriver($v, 'redis'); })->thenInvalid('You have to define driver "redis"')
            ->end()
        ;
    }
}
