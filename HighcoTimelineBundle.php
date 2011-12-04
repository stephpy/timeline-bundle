<?php

namespace Highco\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Highco\TimelineBundle\Compiler\AddSpreadCompilerPass;
use Highco\TimelineBundle\Compiler\AddFilterCompilerPass;

class HighcoTimelineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddSpreadCompilerPass());
        $container->addCompilerPass(new AddFilterCompilerPass());
    }
}

