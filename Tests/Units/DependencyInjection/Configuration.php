<?php

namespace Spy\TimelineBundle\Tests\Units\DependencyInjection;

use mageekguy\atoum;
use Spy\TimelineBundle\DependencyInjection\Configuration as ConfigurationTested;
use Symfony\Component\Config\Definition\Processor;

class Configuration extends atoum\test
{
    public function testNoConfiguration()
    {
        $this->array($this->processConfiguration(array($this->getDefaultInput())))
            ->isEqualTo($this->getDefaultOutput());
    }

    public function testNoDriversAndNoManagers()
    {
        $self = $this;

        $this->exception(function () use ($self) {
            $self->processConfiguration(array(array()));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "spy_timeline": Please define a driver or timeline_manager, action_manager')
            ;
    }

    public function testMultipleDrivers()
    {
        $self = $this;
        $this->exception(function () use ($self) {
            $self->processConfiguration(array(array(
                'drivers' => array(
                    'orm' => array(
                        'object_manager' => 'foo',
                    ),
                    'odm' => array(
                        'object_manager' => 'foo',
                    ),
                ),
            )));
        })
            ->isInstanceOf('\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "spy_timeline.drivers": Please define only one driver.')
            ;
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
            'spread' => array(
                'on_subject' => true,
                'on_global_context' => true,
                'deployer' => 'spy_timeline.spread.deployer.default',
                'batch_size' => '50',
                'delivery' => 'immediate',
            ),
            'render' => array(
                'path' => 'SpyTimelineBundle:Timeline',
                'fallback' => 'SpyTimelineBundle:Timeline:default.html.twig',
                'resources' => array(
                    'SpyTimelineBundle:Action:components.html.twig',
                ),
            ),
            'query_builder' => array(
                'classes' => array(
                    'factory'  => 'Spy\Timeline\Driver\QueryBuilder\QueryBuilderFactory',
                    'asserter' => 'Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter',
                    'operator' => 'Spy\Timeline\Driver\QueryBuilder\Criteria\Operator',
                ),
            ),
            'resolve_component' => array(
                'resolver' => 'spy_timeline.resolve_component.doctrine',
            ),
        );
    }
}
