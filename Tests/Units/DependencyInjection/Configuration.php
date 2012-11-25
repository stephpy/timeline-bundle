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
        $this->array($this->processConfiguration(array($this->getDefaultInput())))
            ->isIdenticalTo($this->getDefaultOutput());
    }

    public function testORMValidation()
    {
        // use driver without define it.
        $config = array(
            'timeline_manager' => 'orm',
            'action_manager' => 'foo',
        );

        $self = $this;

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "orm"');

        // same thing for action_manager
        $config = array(
            'timeline_manager' => 'foo',
            'action_manager' => 'orm',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "orm"');

        // now define driver but not have good classes
        $config = array(
            'drivers' => array(
                'orm' => array(
                    'object_manager' => 'foo',
                ),
            ),
            'timeline_manager' => 'orm',
            'action_manager' => 'foo',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": Please, define timeline class on "orm" driver.');

        // idem for action_manager
        $config = array(
            'drivers' => array(
                'orm' => array(
                    'object_manager' => 'foo',
                ),
            ),
            'timeline_manager' => 'foo',
            'action_manager' => 'orm',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": Please, define action, component, action_comopnent classes on "orm" driver.');

        // now define all.
        $config = array(
            'drivers' => array(
                'orm' => array(
                    'object_manager' => 'foo',
                    'classes' => array(
                        'timeline'         => 'TimelineClass',
                        'action'           => 'ActionClass',
                        'component'        => 'ComponentClass',
                        'action_component' => 'ActionComponentClass'
                    ),
                ),
            ),
            'timeline_manager' => 'orm',
            'action_manager' => 'orm',
        );

        $config = $this->processConfiguration(array($config));

        $this->string($config['timeline_manager'])
                ->isEqualTo('spy_timeline.timeline_manager.orm')
            ->string($config['action_manager'])
                ->isEqualTo('spy_timeline.action_manager.orm');

    }

    public function testODMValidation()
    {
        // use driver without define it.
        $config = array(
            'timeline_manager' => 'odm',
            'action_manager' => 'foo',
        );

        $self = $this;

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "odm"');

        // same thing for action_manager
        $config = array(
            'timeline_manager' => 'foo',
            'action_manager' => 'odm',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "odm"');

        // now define driver but not have good classes
        $config = array(
            'drivers' => array(
                'odm' => array(
                    'object_manager' => 'foo',
                ),
            ),
            'timeline_manager' => 'odm',
            'action_manager' => 'foo',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": Please, define timeline class on "odm" driver.');

        // idem for action_manager
        $config = array(
            'drivers' => array(
                'odm' => array(
                    'object_manager' => 'foo',
                ),
            ),
            'timeline_manager' => 'foo',
            'action_manager' => 'odm',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": Please, define action, component, action_comopnent classes on "odm" driver.');

        // now define all.
        $config = array(
            'drivers' => array(
                'odm' => array(
                    'object_manager' => 'foo',
                    'classes' => array(
                        'timeline'         => 'TimelineClass',
                        'action'           => 'ActionClass',
                        'component'        => 'ComponentClass',
                        'action_component' => 'ActionComponentClass'
                    ),
                ),
            ),
            'timeline_manager' => 'odm',
            'action_manager' => 'odm',
        );

        $config = $this->processConfiguration(array($config));

        $this->string($config['timeline_manager'])
                ->isEqualTo('spy_timeline.timeline_manager.odm')
            ->string($config['action_manager'])
                ->isEqualTo('spy_timeline.action_manager.odm');

    }

    public function testRedisValidation()
    {
        // use driver without define it.
        $config = array(
            'timeline_manager' => 'redis',
            'action_manager' => 'foo',
        );

        $self = $this;

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "redis"');

        // same thing for action_manager
        $config = array(
            'timeline_manager' => 'foo',
            'action_manager' => 'redis',
        );

        $this->exception(function() use ($config, $self) {
            $self->processConfiguration(array($config));
        })
        ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
        ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "redis"');

        // now define driver but not have good classes
        $config = array(
            'drivers' => array(
                'redis' => array(
                    'client' => 'foo',
                ),
            ),
            'timeline_manager' => 'redis',
            'action_manager' => 'redis',
        );

        $config = $this->processConfiguration(array($config));

        $this->string($config['timeline_manager'])
                ->isEqualTo('spy_timeline.timeline_manager.redis')
            ->string($config['action_manager'])
                ->isEqualTo('spy_timeline.action_manager.redis');

    }

    public function processConfiguration($config)
    {
        $processor     = new Processor();
        $configuration = new ConfigurationTested();

        return $processor->processConfiguration($configuration, $config);
    }

    protected function getDefaultInput()
    {
        return array(
            'timeline_manager' => 'foo',
            'action_manager' => 'foo',
        );
    }

    protected function getDefaultOutput()
    {
        return array(
            'timeline_manager' => 'foo',
            'action_manager' => 'foo',
            'notifiers' => array(),
            'filters' => array(),
            'spread' => array(
                'on_subject' => true,
                'on_global_context' => true,
                'deployer' => 'spy_timeline.spread.deployer',
                'delivery' => 'immediate',
            ),
        );
    }
}
