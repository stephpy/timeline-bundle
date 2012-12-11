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

        $this->addDriverSection($rootNode);

        $rootNode
            ->children()
                ->scalarNode('paginator')
                    ->example('spy_timeline.paginator.knp')
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
            ->end();

        $this->addFilterSection($rootNode);
        $this->addSpreadSection($rootNode);
        $this->addRenderSection($rootNode);

        return $tb;
    }

    protected function addDriverSection($rootNode)
    {
        $rootNode
            ->validate()
                ->ifTrue(function($v) {
                    if (!isset($v['drivers']) || count($v['drivers']) == 0) {
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
                                ->scalarNode('prefix')
                                    ->defaultValue('spy_timeline')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('action')
                                            ->defaultValue('Spy\Timeline\Model\Action')
                                        ->end()
                                        ->scalarNode('component')
                                            ->defaultValue('Spy\Timeline\Model\Component')
                                        ->end()
                                        ->scalarNode('action_component')
                                            ->defaultValue('Spy\Timeline\Model\ActionComponent')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addFilterSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('filters')
                    ->children()
                        ->arrayNode('duplicate_key')
                            ->children()
                                ->scalarNode('service')->defaultValue('spy_timeline.filter.duplicate_key')->end()
                                ->scalarNode('priority')->defaultValue(10)->end()
                            ->end()
                        ->end()
                        ->arrayNode('data_hydrator')
                            ->children()
                                ->scalarNode('priority')->defaultValue(20)->end()
                                ->scalarNode('service')->defaultValue('spy_timeline.filter.data_hydrator')->end()
                                ->booleanNode('filter_unresolved')->defaultTrue()->end()
                                ->arrayNode('locators')
                                    ->example(array(
                                        'spy_timeline.filter.data_hydrator.locator.doctrine',
                                    ))
                                    ->prototype('scalar')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addSpreadSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('spread')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('on_subject')->defaultValue(true)->end()
                        ->booleanNode('on_global_context')->defaultValue(true)->end()
                        ->scalarNode('deployer')->defaultValue('spy_timeline.spread.deployer.default')->end()
                        // scalarNode because integerNode introduced on 2.2 only.
                        ->scalarNode('batch_size')->defaultValue('50')->end()
                        ->scalarNode('delivery')->defaultValue('immediate')->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addRenderSection($rootNode)
    {
        $rootNode
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
            ->end();
    }
}
