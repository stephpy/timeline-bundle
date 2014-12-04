<?php

namespace Spy\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $this->addQueryBuilderSection($rootNode);

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
            ->end()
        ;

        $this->addFilterSection($rootNode);
        $this->addSpreadSection($rootNode);
        $this->addRenderSection($rootNode);
        $this->addResolveComponentSection($rootNode);

        return $tb;
    }

    protected function addDriverSection($rootNode)
    {
        $rootNode
            ->validate()
                ->ifTrue(function ($v) {
                    if (!isset($v['drivers']) || count($v['drivers']) == 0) {
                        return !isset($v['timeline_manager']) || !isset($v['action_manager']);
                    }

                    return false;
                })
                ->thenInvalid("Please define a driver or timeline_manager, action_manager")
            ->end()
            ->validate()
                ->ifTrue(function ($v) {
                    return isset($v['drivers']) && isset($v['drivers']['redis']) && $v['spread']['delivery'] != 'immediate';
                })
                ->thenInvalid("Redis driver accepts only spread delivery immediate.")
            ->end()
            ->children()
                ->arrayNode('drivers')
                    ->validate()
                        ->ifTrue(function ($v) {
                            return count($v) > 1;
                        })
                        ->thenInvalid('Please define only one driver.')
                    ->end()
                    ->children()
                        ->arrayNode('orm')
                            ->children()
                                ->scalarNode('object_manager')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->example('doctrine.orm.entity_manager')
                                ->end()
                                ->booleanNode('post_load_listener')
                                    ->defaultValue(false)
                                ->end()
                                ->arrayNode('classes')
                                    ->children()
                                        ->scalarNode('query_builder')
                                            ->defaultValue('Spy\TimelineBundle\Driver\ORM\QueryBuilder\QueryBuilder')
                                        ->end()
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
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->example('doctrine.odm.mongodb.document_manager')
                                ->end()
                                ->booleanNode('post_load_listener')
                                    ->defaultValue(false)
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
            ->end()
        ;
    }

    protected function addQueryBuilderSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('query_builder')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('classes')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('factory')
                                    ->defaultValue('Spy\Timeline\Driver\QueryBuilder\QueryBuilderFactory')
                                ->end()
                                ->scalarNode('asserter')
                                    ->defaultValue('Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter')
                                ->end()
                                ->scalarNode('operator')
                                    ->defaultValue('Spy\Timeline\Driver\QueryBuilder\Criteria\Operator')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
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
            ->end()
        ;
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
            ->end()
        ;
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
                                ->ifTrue(function ($v) { return !in_array('SpyTimelineBundle:Action:components.html.twig', $v); })
                                ->then(function ($v) {
                                    return array_merge(array('SpyTimelineBundle:Action:components.html.twig'), $v);
                                })
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    protected function addResolveComponentSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('resolve_component')
                    ->addDefaultsIfNotSet()
                    ->children()
                       ->scalarNode('resolver')->defaultValue('spy_timeline.resolve_component.doctrine')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
