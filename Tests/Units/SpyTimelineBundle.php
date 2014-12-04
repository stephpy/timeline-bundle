<?php

namespace Spy\TimelineBundle\Tests\Units;

use mageekguy\atoum;
use Spy\TimelineBundle\SpyTimelineBundle as TestedModel;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddLocatorCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddDeliveryMethodCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddComponentDataResolver;

class SpyTimelineBundle extends atoum\test
{
    public function testBuild()
    {
        $this->if($containerBuilder = new \mock\Symfony\Component\DependencyInjection\ContainerBuilder())
            ->and($bundle = new TestedModel())
            ->when($bundle->build($containerBuilder))
            ->then(
                $this->mock($containerBuilder)->call('addCompilerPass')->withArguments(new AddLocatorCompilerPass())->exactly(1)
                ->and($this->mock($containerBuilder)->call('addCompilerPass')->withArguments(new AddDeliveryMethodCompilerPass())->exactly(1))
                ->and($this->mock($containerBuilder)->call('addCompilerPass')->withArguments(new AddComponentDataResolver())->exactly(1))
            )
        ;
    }
}
