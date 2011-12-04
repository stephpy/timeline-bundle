<?php

namespace Highco\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
			->end();

        return $tb;
    }
}
