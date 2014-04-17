<?php

namespace Spy\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddSpreadCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddFilterCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddRegistryCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddDeliveryMethodCompilerPass;

/**
 * SpyTimelineBundle
 *
 * @uses Bundle
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class SpyTimelineBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddSpreadCompilerPass());
        $container->addCompilerPass(new AddFilterCompilerPass());
        $container->addCompilerPass(new AddRegistryCompilerPass());
        $container->addCompilerPass(new AddDeliveryMethodCompilerPass());
    }
}
