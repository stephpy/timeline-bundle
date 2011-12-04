<?php

namespace Highco\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Highco\TimelineBundle\Compiler\AddSpreadCompilerPass;

class HighcoTimelineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddSpreadCompilerPass());
    }
}

