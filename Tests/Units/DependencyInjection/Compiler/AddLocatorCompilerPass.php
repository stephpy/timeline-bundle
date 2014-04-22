<?php

namespace Spy\TimelineBundle\Tests\Units\DependencyInjection\Compiler;

require_once __DIR__ . "/../../../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddLocatorCompilerPass as TestedModel;
use Spy\Timeline\Filter\DataHydrator;

/**
 * Class AddLocatorCompilerPass
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class AddLocatorCompilerPass extends Test
{
    public function testProcess()
    {
        //there are 4 (3 unique) config locator services so there should be only 3 addLocator calls
        $configLocators = array('foo.service', 'bar.service');
        $taggedServicesResult = array('baz.service' => array(), 'foo.service' => array());

        //setup mocks
        $this->if($this->mockClass('\Symfony\Component\DependencyInjection\ContainerBuilder', '\Mock'))
            ->and($this->mockGenerator->orphanize('__construct'))
            ->and($this->mockGenerator->shuntParentClassCalls())
            ->and($this->mockClass('\Symfony\Component\DependencyInjection\Definition'))
            ->and($this->mockClass('Spy\Timeline\Filter\DataHydrator', '\Mock'))
            //mock classes
            ->and($hydrator = new \Mock\DataHydrator())
            ->and($containerBuilder = new \Mock\ContainerBuilder())
            ->and($definition = new \Mock\Definition())
            //it asks for the locators parameter
            ->and($this->calling($containerBuilder)->getParameter = function($argument) use ($configLocators) {
                switch($argument)
                {
                    case "spy_timeline.filter.data_hydrator.locators_config":
                        return $configLocators;
                }
            })
            //it asks for definition we return a definition instance
            ->and($this->calling($containerBuilder)->getDefinition = function() use ($definition) {
                return $definition;
            })
            //Tagged services finds one unique id and one id which is already in the config
            ->and($this->calling($containerBuilder)->findTaggedServiceIds = function() use ($taggedServicesResult) {
                return $taggedServicesResult;
            })

            ->and($compiler = new TestedModel())
            ->when($compiler->process($containerBuilder))
            ->then(
                $this->mock($containerBuilder)->call('getParameter')->withArguments('spy_timeline.filter.data_hydrator.locators_config')->exactly(1)
                ->and($this->mock($containerBuilder)->call('findTaggedServiceIds')->withArguments('spy_timeline.filter.data_hydrator.locator')->exactly(1))
                //there are 4 definitions found but foo.config_locator was in tagged services and in the config so it gets called twice.
                ->and($this->mock($containerBuilder)->call('getDefinition')->withArguments('foo.service')->exactly(2))
                ->and($this->mock($containerBuilder)->call('getDefinition')->withArguments('bar.service')->exactly(1))
                ->and($this->mock($containerBuilder)->call('getDefinition')->withArguments('baz.service')->exactly(1))
                //it only calls addlocator three times since foo.config_locator is found twice and filtered
                ->and($this->mock($definition)->call('addMethodCall')->withArguments('addLocator', array($definition))->exactly(3))
            )
        ;
    }
}
