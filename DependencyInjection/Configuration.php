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
                ->arrayNode('drivers')
                    ->children()
                        ->arrayNode('orm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->defaultValue('doctrine.orm.entity_manager')
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('classes')
                                    ->children()
                                        ->scalarNode('timeline')->example('Acme\YourBundle\Entity\Timeline')->end()
                                        ->scalarNode('action')->example('Acme\YourBundle\Entity\Action')->end()
                                        ->scalarNode('component')->example('Acme\YourBundle\Entity\Component')->end()
                                        ->scalarNode('action_component')->example('Acme\YourBundle\Entity\ActionComponent')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('odm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->defaultValue('doctrine.odm.entity_manager')
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('classes')
                                    ->children()
                                        ->scalarNode('timeline')->example('Acme\YourBundle\Document\Timeline')->end()
                                        ->scalarNode('action')->example('Acme\YourBundle\Document\Action')->end()
                                        ->scalarNode('component')->example('Acme\YourBundle\Document\Component')->end()
                                        ->scalarNode('action_component')->example('Acme\YourBundle\Document\ActionComponent')->end()
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
                                ->scalarNode('timeline_key_prefix')->defaultValue('timeline:')->end()
                                ->scalarNode('action_key_prefix')->defaultValue('timeline:action')->end()
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
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->example('orm')
                ->end()
                ->scalarNode('action_manager')
                    ->beforeNormalization()
                        ->ifInArray(array('orm', 'odm','redis'))
                        ->then(function ($v) { return sprintf('spy_timeline.action_manager.%s', $v); })
                    ->end()
                    ->isRequired()
                    ->cannotBeEmpty()
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
        /* --- validate than driver is defined if used via managers --- */
        $hasDriverRequest = function($v, $driver) {
            $timelineManager  = sprintf('spy_timeline.timeline_manager.%s', $driver);
            $actionManager    = sprintf('spy_timeline.action_manager.%s', $driver);

            if ((isset($v['timeline_manager']) && $v['timeline_manager'] == $timelineManager) ||
                (isset($v['action_manager']) && $v['action_manager'] == $actionManager)) {
                return false;
            }

            return true;
        };

        $validateDriver = function($v, $driver) use ($hasDriverRequest) {
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
            ->end();

        /* --- validate than timeline class is defined for ORM and ODM drivers --- */

        $getClassForDriver = function ($v, $class, $driver) {
            if (isset($v['drivers']) &&
                isset($v['drivers'][$driver]) &&
                isset($v['drivers'][$driver]['classes']) &&
                isset($v['drivers'][$driver]['classes'][$class])) {
                return $v['drivers'][$driver]['classes'][$class];
            }
        };

        $validateTimelineClasses = function($v, $driver) use ($getClassForDriver) {
            $timelineManager  = sprintf('spy_timeline.timeline_manager.%s', $driver);

            if ($v['timeline_manager'] != $timelineManager) {
                return true;
            }

            return null !== $getClassForDriver($v, 'timeline', $driver);
        };

        $treeBuilder->validate()
                ->ifTrue(function ($v) use ($validateTimelineClasses) { return !$validateTimelineClasses($v, 'orm'); })->thenInvalid('Please, define timeline class on "orm" driver.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateTimelineClasses) { return !$validateTimelineClasses($v, 'odm'); })->thenInvalid('Please, define timeline class on "odm" driver.')
            ->end();

        /* --- validate than action, component, action_component classes are defined for ORM and ODM drivers --- */
        $validateActionClasses = function($v, $driver) use ($getClassForDriver) {
            $actionManager  = sprintf('spy_timeline.action_manager.%s', $driver);

            if ($v['action_manager'] != $actionManager) {
                return true;
            }

            return null !== $getClassForDriver($v, 'action', $driver) &&
                null !== $getClassForDriver($v, 'component', $driver) &&
                null !== $getClassForDriver($v, 'action_component', $driver);
        };

        $treeBuilder->validate()
                ->ifTrue(function ($v) use ($validateActionClasses) { return !$validateActionClasses($v, 'orm'); })->thenInvalid('Please, define action, component, action_component classes on "orm" driver.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateActionClasses) { return !$validateActionClasses($v, 'odm'); })->thenInvalid('Please, define action, component, action_component classes on "odm" driver.')
            ->end()
        ;

        $validateTwiceClass = function($v, $class) {
            $ormClasses = isset($v['drivers']) && isset($v['drivers']['orm']) && isset($v['drivers']['orm']['classes']) ? $v['drivers']['orm']['classes'] : array();
            $odmClasses = isset($v['drivers']) && isset($v['drivers']['odm']) && isset($v['drivers']['orm']['classes']) ? $v['drivers']['odm']['classes'] : array();

            return !(isset($ormClasses[$class]) && isset($odmClasses[$class]));
        };

        $treeBuilder->validate()
                ->ifTrue(function ($v) use ($validateTwiceClass) { return !$validateTwiceClass($v, 'timeline'); })->thenInvalid('Please, define timeline class one time.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateTwiceClass) { return !$validateTwiceClass($v, 'action'); })->thenInvalid('Please, define action class one time.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateTwiceClass) { return !$validateTwiceClass($v, 'component'); })->thenInvalid('Please, define component class one time.')
            ->end()
            ->validate()
                ->ifTrue(function ($v) use ($validateTwiceClass) { return !$validateTwiceClass($v, 'action_component'); })->thenInvalid('Please, define action_component class one time.')
            ->end()
        ;
    }
}
