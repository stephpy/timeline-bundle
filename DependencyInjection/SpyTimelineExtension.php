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

        if (!in_array(strtolower($config['db_driver']), array('orm', 'redis'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }

        $loader->load(sprintf('%s.xml', $config['db_driver']));

        $container->setAlias('spy_timeline_action_manager', $config['timeline_action_manager']);
        $container->setParameter('spy_timeline.db_driver', $config['db_driver']);

        $loader->load('deployer.xml');
        $loader->load('filter.xml');
        $loader->load('manager.xml');
        $loader->load('notification.xml');
        $loader->load('pager.xml');
        $loader->load('spreads.xml');
        $loader->load('twig.xml');

        if (!empty($config['timeline_action_class'])) {
            $container->setParameter('spy_timeline_action.model.class', $config['timeline_action_class']);
        }

        /* --- notifiers --- */
        $notifiers = $config['notifiers'];
        $definition = $container->getDefinition('spy_timeline.notification_manager');

        foreach ($notifiers as $notifier) {
            $definition->addMethodCall('addNotifier', array(new Reference($notifier)));
        }

        /* --- spread --- */
        $spread = isset($config['spread']) ? $config['spread'] : array();

        $definition = $container->getDefinition('spy_timeline.spread.manager');
        $definition->addArgument(array(
            'onMe' => isset($spread['on_me']) ? $spread['on_me'] : true,
            'onGlobalContext' => isset($spread['on_global_context']) ? $spread['on_global_context'] : true,
        ));

        /* ---- provider ---- */
        if (isset($config['provider']['object_manager'])) {
            $container->setAlias('spy_timeline.provider.object_manager', $config['provider']['object_manager']);
        }
        if (isset($config['provider']['timeline_class'])) {
            $container->setParameter('spy_timeline.provider.timeline_class', $config['provider']['timeline_class']);
        }

        if (isset($config['provider']['service'])) {
            $container->setAlias('spy_timeline.provider', $config['provider']['service']);
        } elseif (isset($config['provider']['type'])) {
            $loader->load(sprintf('provider/%s.xml', $config['provider']['type']));
        }

        /* ---- delivery ---- */
        if ($config['delivery'] == Deployer::DELIVERY_WAIT && $config['db_driver'] == 'redis') {
            throw new \InvalidArgumentException('Delivery wait and db_driver redis cannot work together');
        }

        $container->setParameter('spy_timeline.spread.deployer.delivery', $config['delivery']);

        /* ---- render ---- */
        $render = $config['render'];
        $container->setParameter('spy_timeline.render.path', $render['path']);
        $container->setParameter('spy_timeline.render.fallback', $render['fallback']);
        $container->setParameter('spy_timeline.render.i18n.fallback', isset($render['i18n']['fallback']) ? $render['i18n']['fallback'] : null );
        $container->setParameter('spy_timeline.twig.resources', $render['resources']);
    }
}
