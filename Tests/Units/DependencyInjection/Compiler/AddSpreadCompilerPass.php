<?php

namespace Spy\TimelineBundle\Tests\Units\DependencyInjection\Compiler;

use mageekguy\atoum;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddSpreadCompilerPass as TestedModel;

class AddSpreadCompilerPass extends atoum\test
{
    public function testProcess()
    {
        //there are 3 spreaders, with 2 of them under the same priority
        $taggedServicesResult = array('foo.spread' => array(array('priority' => 10)), 'bar.spread' => array(), 'baz.spread' => array(array('priority' => 10)));

        $this
            ->given($containerBuilder = new \mock\Symfony\Component\DependencyInjection\ContainerBuilder())
            ->and($this->mockGenerator->orphanize('__construct'))
            ->and($this->mockGenerator->shuntParentClassCalls())
            ->and($definition = new \mock\Symfony\Component\DependencyInjection\Definition())
            ->and($this->calling($containerBuilder)->getAlias = function ($alias) {
                return $alias;
            })
            ->and($this->calling($containerBuilder)->getDefinition = function () use ($definition) {
                return $definition;
            })
            ->and($this->calling($containerBuilder)->findTaggedServiceIds = function () use ($taggedServicesResult) {
                return $taggedServicesResult;
            })
            ->and($compiler = new TestedModel())
            ->when($compiler->process($containerBuilder))
            ->then(
                $this->mock($containerBuilder)->call('getAlias')->withArguments('spy_timeline.spread.deployer')->exactly(1)
                    ->and($this->mock($containerBuilder)->call('findTaggedServiceIds')->withArguments('spy_timeline.spread')->exactly(1))

                    ->and($this->mock($containerBuilder)->call('getDefinition')->withArguments('foo.spread')->exactly(1))
                    ->and($this->mock($containerBuilder)->call('getDefinition')->withArguments('bar.spread')->exactly(1))
                    ->and($this->mock($containerBuilder)->call('getDefinition')->withArguments('baz.spread')->exactly(1))

                    //it calls addSpread three times
                    ->and($this->mock($definition)->call('addMethodCall')->withArguments('addSpread', array($definition))->exactly(3))
            )
        ;
    }

}
