<?php

namespace Highco\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Highco\TimelineBundle\Compiler\AddSpreadCompilerPass;

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
        $container->addCompilerPass(new AddSpreadCompilerPass());
    }
}

