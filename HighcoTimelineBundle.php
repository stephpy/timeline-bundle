<?php

namespace Highco\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Highco\TimelineBundle\DependencyInjection\Compiler\AddSpreadCompilerPass;
use Highco\TimelineBundle\DependencyInjection\Compiler\AddFilterCompilerPass;

/**
 * HighcoTimelineBundle
 *
 * @uses Bundle
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class HighcoTimelineBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddFilterCompilerPass());
        $container->addCompilerPass(new AddSpreadCompilerPass());
    }
}

