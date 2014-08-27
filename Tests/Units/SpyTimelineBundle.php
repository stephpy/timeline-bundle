<?php

namespace Spy\TimelineBundle\Tests\Units;

require_once __DIR__."/../../vendor/autoload.php";

use Spy\TimelineBundle\DependencyInjection\Compiler\AddComponentDataResolver;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddDeliveryMethodCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddLocatorCompilerPass;
use Spy\TimelineBundle\SpyTimelineBundle as TestedModel;
use atoum\AtoumBundle\Test\Units\Test;

class SpyTimelineBundle extends Test
{
    public function testBuild()
    {
        $this->if($this->mockClass('\Symfony\Component\DependencyInjection\ContainerBuilder', '\Mock'))
            ->and($containerBuilder = new \Mock\ContainerBuilder())
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
