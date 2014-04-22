<?php

namespace Spy\TimelineBundle\Tests\Units;

require_once __DIR__ . "/../../vendor/autoload.php";

use atoum\AtoumBundle\Test\Units\Test;
use Spy\TimelineBundle\SpyTimelineBundle as TestedModel;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddLocatorCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddDeliveryMethodCompilerPass;

/**
 * Class SpyTimelineBundle
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
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
                and($this->mock($containerBuilder)->call('addCompilerPass')->withArguments(new AddDeliveryMethodCompilerPass())->exactly(1))
            )
        ;
    }
}
