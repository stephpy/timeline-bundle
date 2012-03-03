<?php

namespace Highco\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Highco\TimelineBundle\Compiler\AddSpreadCompilerPass;

/**
 * @uses Bundle
 * @package HighcoTimelineBundle
 * @version 1.0.0
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

