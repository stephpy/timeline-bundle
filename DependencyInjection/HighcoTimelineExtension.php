<?php

namespace Highco\TimelineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Highco\TimelineBundle\Spread\Deployer;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class HighcoTimelineExtension extends Extension
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

        $container->setAlias('highco.timeline_action_manager', $config['timeline_action_manager']);
        $container->setParameter('highco.timeline.db_driver', $config['db_driver']);

        $loader->load('deployer.xml');
        $loader->load('filter.xml');
        $loader->load('manager.xml');
        $loader->load('notification.xml');
        $loader->load('pager.xml');
        $loader->load('provider.xml');
        $loader->load('spreads.xml');
        $loader->load('twig.xml');

        if (!empty($config['timeline_action_class'])) {
            $container->setParameter('highco.timeline_action.model.class', $config['timeline_action_class']);
        }

        /* --- notifiers --- */
        $notifiers = $config['notifiers'];
        $definition = $container->getDefinition('highco.timeline.notification_manager');

        foreach ($notifiers as $notifier) {
            $definition->addMethodCall('addNotifier', array(new Reference($notifier)));
        }

        /* --- spread --- */
        $spread = isset($config['spread']) ? $config['spread'] : array();

        $definition = $container->getDefinition('highco.timeline.spread.manager');
        $definition->addArgument(array(
            'onMe' => isset($spread['on_me']) ? $spread['on_me'] : true,
            'onGlobalContext' => isset($spread['on_global_context']) ? $spread['on_global_context'] : true,
        ));

        /* ---- provider ---- */
        $providerDefinition = $container->getDefinition($config['provider']);

        $container->setAlias('highco.timeline.provider', $config['provider']);

        $container->getDefinition('highco.timeline.manager')
            ->replaceArgument(2, $providerDefinition);

        $container->getDefinition('highco.timeline.spread.deployer')
            ->replaceArgument(2, $providerDefinition);

        /* ---- delivery ---- */
        if ($config['delivery'] == Deployer::DELIVERY_WAIT && $config['db_driver'] == 'redis') {
            throw new \InvalidArgumentException('Delivery wait and db_driver redis cannot work together');
        }

        $container->setParameter('highco.timeline.spread.deployer.delivery', $config['delivery']);

        /* ---- render ---- */
        $render = $config['render'];
        $container->setParameter('highco.timeline.render.path', $render['path']);
        $container->setParameter('highco.timeline.render.fallback', $render['fallback']);
    }
}
