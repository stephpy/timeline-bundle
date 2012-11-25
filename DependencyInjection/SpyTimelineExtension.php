<?php

namespace Spy\TimelineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Spy\TimelineBundle\Spread\Deployer;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
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

        $container->setParameter('spy_timeline.class.timeline', $config['classes']['timeline']);
        $container->setParameter('spy_timeline.class.action', $config['classes']['action']);
        $container->setParameter('spy_timeline.class.component', $config['classes']['component']);

        if (isset($config['drivers'])) {

            if (isset($config['drivers']['orm'])) {
                $container->setAlias('spy_timeline.driver.orm.object_manager', $config['drivers']['orm']['object_manager']);
                $loader->load('driver/orm.xml');
            }

            if (isset($config['drivers']['odm'])) {
                $container->setAlias('spy_timeline.driver.odm.object_manager', $config['drivers']['odm']['object_manager']);
                $loader->load('driver/odm.xml');
            }

            if (isset($config['drivers']['redis'])) {
                $container->setAlias('spy_timeline.driver.redis.client', $config['drivers']['redis']['client']);
                $loader->load('driver/redis.xml');
            }
        }

        $container->setAlias('spy_timeline.timeline_manager', $config['timeline_manager']);
        $container->setAlias('spy_timeline.action_manager', $config['action_manager']);
        $container->setAlias('spy_timeline.component_manager', $config['component_manager']);
    }
}
