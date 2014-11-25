<?php

namespace Spy\TimelineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;

class SpyTimelineExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('filter.xml');
        $loader->load('notification.xml');
        $loader->load('paginator.xml');
        $loader->load('resolve_component.xml');
        $loader->load('result_builder.xml');
        $loader->load('spread.xml');
        $loader->load('twig.xml');

        $driver = null;

        if (isset($config['drivers'])) {
            if (isset($config['drivers']['orm'])) {
                $this->loadORMDriver($container, $loader, $config['drivers']['orm']);
                $driver = 'orm';
            } elseif (isset($config['drivers']['odm'])) {
                $this->loadODMDriver($container, $loader, $config['drivers']['odm']);
                $driver = 'odm';
            } elseif (isset($config['drivers']['redis'])) {
                $this->loadRedisDriver($container, $loader, $config['drivers']['redis']);
                $driver = 'redis';
            }
        }

        if (!$driver) {
            $timelineManager = $config['timeline_manager'];
            $actionManager   = $config['action_manager'];
        } else {
            $timelineManager = isset($config['timeline_manager']) ? $config['timeline_manager'] : sprintf('spy_timeline.timeline_manager.%s', $driver);
            $actionManager   = isset($config['action_manager'])   ? $config['action_manager'] : sprintf('spy_timeline.action_manager.%s', $driver);
        }

        $container->setAlias('spy_timeline.timeline_manager', $timelineManager);
        $container->setAlias('spy_timeline.action_manager', $actionManager);

        // pager
        if (isset($config['paginator']) && !empty($config['paginator'])) {
            $paginator = $config['paginator'];
        } else {
            $paginator = sprintf('spy_timeline.pager.%s', $driver);
        }

        // filters
        $filters       = isset($config['filters']) ? $config['filters'] : array();
        $filterManager = $container->getDefinition('spy_timeline.filter.manager');

        if (isset($filters['duplicate_key'])) {
            $filter  = $filters['duplicate_key'];
            $service = $container->getDefinition($filter['service']);
            $service->addMethodCall('setPriority', array($filter['priority']));

            $filterManager->addMethodCall('add', array($service));
        }

        if (isset($filters['data_hydrator'])) {
            $filter  = $filters['data_hydrator'];

            $service = $container->getDefinition($filter['service']);
            $service->addArgument($filter['filter_unresolved']);
            $service->addMethodCall('setPriority', array($filter['priority']));

            $container->setParameter('spy_timeline.filter.data_hydrator.locators_config', $filter['locators']);

            $filterManager->addMethodCall('add', array($service));
        }

        // result builder
        $definition = $container->getDefinition('spy_timeline.result_builder');
        $definition->addArgument($container->getDefinition(sprintf('spy_timeline.query_executor.%s', $driver)));
        $definition->addArgument($filterManager);

        if ($paginator) {
            $definition->addMethodCall('setPager', array($container->getDefinition($paginator)));
        }

        // spreads
        $container->setAlias('spy_timeline.spread.deployer', $config['spread']['deployer']);
        $container->setParameter('spy_timeline.spread.deployer.delivery', $config['spread']['delivery']);
        $container->setParameter('spy_timeline.spread.on_subject', $config['spread']['on_subject']);
        $container->setParameter('spy_timeline.spread.on_global_context', $config['spread']['on_global_context']);
        $container->setParameter('spy_timeline.spread.deployer.batch_size', $config['spread']['batch_size']);

        // notifiers
        $notifiers  = $config['notifiers'];
        $definition = $container->getDefinition($config['spread']['deployer']);

        foreach ($notifiers as $notifier) {
            $definition->addMethodCall('addNotifier', array(new Reference($notifier)));
        }

        //twig
        $render = $config['render'];
        $container->setParameter('spy_timeline.render.path', $render['path']);
        $container->setParameter('spy_timeline.render.fallback', $render['fallback']);
        $container->setParameter('spy_timeline.render.i18n.fallback', isset($render['i18n']) && isset($render['i18n']['fallback']) ? $render['i18n']['fallback'] : null);
        $container->setParameter('spy_timeline.twig.resources', $render['resources']);

        // query_builder
        $queryBuilder = $config['query_builder'];
        $container->setParameter('spy_timeline.query_builder.factory.class', $queryBuilder['classes']['factory']);
        $container->setParameter('spy_timeline.query_builder.asserter.class', $queryBuilder['classes']['asserter']);
        $container->setParameter('spy_timeline.query_builder.operator.class', $queryBuilder['classes']['operator']);

        // resolve_component
        $resolveComponent = $config['resolve_component'];
        $container->setAlias('spy_timeline.resolve_component.resolver', $resolveComponent['resolver']);

        // sets a parameter which we use in the addRegistryCompilerPass (there should be a cleaner way)
        if ($resolveComponent['resolver'] === 'spy_timeline.resolve_component.doctrine') {
            $container->setParameter('spy_timeline.resolve_component.doctrine_registries', true);
        }
    }

    private function loadORMDriver($container, $loader, $config)
    {
        $classes = isset($config['classes']) ? $config['classes'] : array();

        $parameters = array(
            'timeline', 'action', 'component', 'action_component',
        );

        foreach ($parameters as $parameter) {
            if (isset($classes[$parameter])) {
                $container->setParameter(sprintf('spy_timeline.class.%s', $parameter), $classes[$parameter]);
            }
        }

        $container->setAlias('spy_timeline.driver.object_manager', $config['object_manager']);

        $loader->load('driver/orm.xml');

        if ($config['post_load_listener']) {
            $loader->load('driver/doctrine/orm_listener.xml');
        }

        if (isset($classes['query_builder'])) {
            $container->setParameter('spy_timeline.query_builder.class', $classes['query_builder']);
        }

        $loader->load('query_builder.xml');
        $container->setAlias('spy_timeline.query_builder', 'spy_timeline.query_builder.orm');
    }

    private function loadODMDriver($container, $loader, $config)
    {
        $classes = isset($config['classes']) ? $config['classes'] : array();

        $parameters = array(
            'timeline', 'action', 'component', 'action_component',
        );

        foreach ($parameters as $parameter) {
            if (isset($classes[$parameter])) {
                $container->setParameter(sprintf('spy_timeline.class.%s', $parameter), $classes[$parameter]);
            }
        }

        $container->setAlias('spy_timeline.driver.object_manager', $config['object_manager']);

        $loader->load('driver/odm.xml');

        if ($config['post_load_listener']) {
            $loader->load('driver/doctrine/odm_listener.xml');
        }
    }

    private function loadRedisDriver($container, $loader, $config)
    {
        $classes = isset($config['classes']) ? $config['classes'] : array();

        $parameters = array(
            'action', 'component', 'action_component',
        );

        foreach ($parameters as $parameter) {
            if (isset($classes[$parameter])) {
                $container->setParameter(sprintf('spy_timeline.class.%s', $parameter), $classes[$parameter]);
            }
        }

        $container->setParameter('spy_timeline.driver.redis.pipeline', $config['pipeline']);
        $container->setParameter('spy_timeline.driver.redis.prefix', $config['prefix']);

        $container->setAlias('spy_timeline.driver.redis.client', $config['client']);

        $loader->load('driver/redis.xml');
    }
}
