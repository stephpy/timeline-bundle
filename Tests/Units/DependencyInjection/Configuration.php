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
            ->and($config = array($this->getDefaultInput()))
            ->array($result = $processor->processConfiguration($configuration, $config))
            ->isIdenticalTo($this->getDefaultOutput())
        ;
    }

    /**
     * @dataProvider getAllDrivers
     */
    public function testDriverValidations($driver, $options)
    {
        $this->if($processor = new Processor())
            ->and($configuration = new ConfigurationTested())
            ->and($config = array($this->getDefaultInput()))
            ->and($config[0]['drivers'] = array(
                $driver => $options,
            ))
            ->and($config[0]['timeline_manager'] = $driver)
            ->and($config[0]['action_manager'] = $driver)
            ->and($config[0]['component_manager'] = $driver)
            ->array($result = $processor->processConfiguration($configuration, $config))
            ->string($result['timeline_manager'])->isEqualTo('spy_timeline.timeline_manager.'.$driver)
            ->string($result['action_manager'])->isEqualTo('spy_timeline.action_manager.'.$driver)
            ->string($result['component_manager'])->isEqualTo('spy_timeline.component_manager.'.$driver);

        $this->if($config = array($this->getDefaultInput()))
            ->and($config[0]['timeline_manager'] = $driver)
            ->and($config[0]['action_manager'] = $driver)
            ->and($config[0]['component_manager'] = $driver)
            ->exception(function() use ($processor, $configuration, $config) {
                $result = $processor->processConfiguration($configuration, $config);
            })
            ->isInstanceOf('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException')
            ->hasMessage('Invalid configuration for path "spy_timeline": You have to define driver "'.$driver.'"');
    }

    public function getAllDrivers()
    {
        return array(
            array('orm', array('object_manager' => 'foo')),
            array('odm', array('object_manager' => 'foo')),
            array('redis', array('client' => 'foo')),
        );
    }

    protected function getDefaultInput()
    {
        return array(
            'classes' => array(
                'timeline' => 'foo',
                'action' => 'foo',
                'component' => 'foo',
            ),
            'timeline_manager' => 'foo',
            'action_manager' => 'foo',
            'component_manager' => 'foo',
        );
    }

    protected function getDefaultOutput()
    {
        return array(
            'classes' => array(
                'timeline' => 'foo',
                'action' => 'foo',
                'component' => 'foo',
            ),
            'timeline_manager' => 'foo',
            'action_manager' => 'foo',
            'component_manager' => 'foo',
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
