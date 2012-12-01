<?php

namespace Highco\TimelineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\Config\FileLocator;
use Highco\TimelineBundle\Spread\Deployer;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Chris Jones <leeked@gmail.com>
 */
class HighcoTimelineExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration($container->get('kernel.debug'));

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        // Handle the MongoDB document manager name in a specific way as it does not have a registry to make it easy
        // TODO: change it when bumping the requirement to Symfony 2.1
        if ('mongodb' === $config['db_driver']) {
            if (null === $config['model_manager_name']) {
                $container->setAlias('highco_timeline.document_manager', new Alias(
                    'doctrine.odm.mongodb.document_manager', false
                ));
            } else {
                $container->setAlias('highco_timeline.document_manager', new Alias(
                    sprintf('doctrine.odm.%s_mongodb.document_manager',
                    $config['model_manager_name']),
                    false
                ));
            }
        }

        $container->setAlias('highco.timeline_action_manager', $config['timeline_action_manager']);
        $container->setParameter('highco.timeline.db_driver', $config['db_driver']);

        $loader->load('deployer.xml');
        $loader->load('filter.xml');
        $loader->load('manager.xml');
        $loader->load('notification.xml');
        $loader->load('pager.xml');
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
        if (isset($config['provider']['object_manager'])) {
            $container->setAlias('highco.timeline.provider.object_manager', $config['provider']['object_manager']);
        }
        if (isset($config['provider']['timeline_class'])) {
            $container->setParameter('highco.timeline.provider.timeline_class', $config['provider']['timeline_class']);
        }

        if (isset($config['provider']['service'])) {
            $container->setAlias('highco.timeline.provider', $config['provider']['service']);
        } elseif (isset($config['provider']['type'])) {
            $loader->load(sprintf('provider/%s.xml', $config['provider']['type']));
        }

        /* ---- delivery ---- */
        if ($config['delivery'] == Deployer::DELIVERY_WAIT && $config['db_driver'] == 'redis') {
            throw new \InvalidArgumentException('Delivery wait and db_driver redis cannot work together');
        }

        $container->setParameter('highco.timeline.spread.deployer.delivery', $config['delivery']);

        /* ---- render ---- */
        $render = $config['render'];
        $container->setParameter('highco.timeline.render.path', $render['path']);
        $container->setParameter('highco.timeline.render.fallback', $render['fallback']);
        $container->setParameter('highco.timeline.render.i18n.fallback', isset($render['i18n']['fallback']) ? $render['i18n']['fallback'] : null );
        $container->setParameter('highco.timeline.twig.resources', $render['resources']);
    }
}
