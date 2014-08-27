<?php

namespace Spy\TimelineBundle;

use Spy\TimelineBundle\DependencyInjection\Compiler\AddComponentDataResolver;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddDeliveryMethodCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddFilterCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddLocatorCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddRegistryCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddSpreadCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
        $container->addCompilerPass(new AddLocatorCompilerPass());
        $container->addCompilerPass(new AddComponentDataResolver());
    }
}
