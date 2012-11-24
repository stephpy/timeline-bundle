<?php

namespace Spy\TimelineBundle\Tests\Units\DependencyInjection;

require_once __DIR__."/../../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Spy\TimelineBundle\DependencyInjection\Configuration as ConfigurationTested;
use Symfony\Component\Config\Definition\Processor;

class Configuration extends Test
{
    public function testNoConfiguration()
    {
        $this->if($processor = new Processor())
            ->and($configuration = new ConfigurationTested())
            ->and($config = array())
            ->array($result = $processor->processConfiguration($configuration, $config))
                ->isIdenticalTo(array('notifiers' => array(), 'filters' => array()))
        ;
    }

    public function testORMValidation()
    {
        $this->if($processor = new Processor())
            ->and($configuration = new ConfigurationTested())
            ->and($config = array(
                array(
                    'drivers' => array(
                        'orm' => array(
                            'object_manager' => 'doctrine.orm.entity_manager'
                        )
                    ),
                    'timeline_manager' => 'orm',
                    'action_manager' => 'orm',
                    'component_manager' => 'orm',
                )
            ))
            ->array($result = $processor->processConfiguration($configuration, $config))
            ->isIdenticalTo(array(
                'drivers' => array(
                    'orm' => array(
                        'object_manager' => 'doctrine.orm.entity_manager'
                    )
                ),
                'timeline_manager' => 'spy_timeline.timeline_manager.orm', // alias
                'action_manager' => 'spy_timeline.action_manager.orm', // alias
                'component_manager' => 'spy_timeline.component_manager.orm', //alias
                'notifiers' => array(),
                'filters' => array(),
            ))
        ;

        // now i try to add add an orm manager without orm driver defined.

        $this->if($config = array(
            array(
                'timeline_manager' => 'orm',
                'action_manager' => 'orm',
                'component_manager' => 'orm',
            )
        ))
        ->exception(function() use ($processor, $configuration, $config) {
            $result = $processor->processConfiguration($configuration, $config);
        })
            ->isInstanceOf('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "orm"')
        ;
    }

    public function testODMValidation()
    {
        $this->if($processor = new Processor())
            ->and($configuration = new ConfigurationTested())
            ->and($config = array(
                array(
                    'drivers' => array(
                        'odm' => array(
                            'object_manager' => 'doctrine.odm.entity_manager'
                        )
                    ),
                    'timeline_manager' => 'odm',
                    'action_manager' => 'odm',
                    'component_manager' => 'odm',
                )
            ))
            ->array($result = $processor->processConfiguration($configuration, $config))
            ->isIdenticalTo(array(
                'drivers' => array(
                    'odm' => array(
                        'object_manager' => 'doctrine.odm.entity_manager'
                    )
                ),
                'timeline_manager' => 'spy_timeline.timeline_manager.odm', // alias
                'action_manager' => 'spy_timeline.action_manager.odm', // alias
                'component_manager' => 'spy_timeline.component_manager.odm', //alias
                'notifiers' => array(),
                'filters' => array(),
            ))
        ;

        // now i try to add add an odm manager without odm driver defined.

        $this->if($config = array(
            array(
                'timeline_manager' => 'odm',
                'action_manager' => 'odm',
                'component_manager' => 'odm',
            )
        ))
        ->exception(function() use ($processor, $configuration, $config) {
            $result = $processor->processConfiguration($configuration, $config);
        })
            ->isInstanceOf('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "odm"')
        ;
    }

    public function testRedisValidation()
    {
        $this->if($processor = new Processor())
            ->and($configuration = new ConfigurationTested())
            ->and($config = array(
                array(
                    'drivers' => array(
                        'redis' => array(
                            'client' => 'my_client'
                        )
                    ),
                    'timeline_manager' => 'redis',
                    'action_manager' => 'redis',
                    'component_manager' => 'redis',
                )
            ))
            ->array($result = $processor->processConfiguration($configuration, $config))
            ->isIdenticalTo(array(
                'drivers' => array(
                    'redis' => array(
                        'client' => 'my_client'
                    )
                ),
                'timeline_manager' => 'spy_timeline.timeline_manager.redis', // alias
                'action_manager' => 'spy_timeline.action_manager.redis', // alias
                'component_manager' => 'spy_timeline.component_manager.redis', //alias
                'notifiers' => array(),
                'filters' => array(),
            ))
        ;

        // now i try to add add an redis manager without redis driver defined.

        $this->if($config = array(
            array(
                'timeline_manager' => 'redis',
                'action_manager' => 'redis',
                'component_manager' => 'redis',
            )
        ))
        ->exception(function() use ($processor, $configuration, $config) {
            $result = $processor->processConfiguration($configuration, $config);
        })
            ->isInstanceOf('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "redis"')
        ;
    }


}
