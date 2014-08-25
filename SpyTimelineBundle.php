<?php

namespace Spy\TimelineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddSpreadCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddFilterCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddRegistryCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddDeliveryMethodCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddLocatorCompilerPass;
use Spy\TimelineBundle\DependencyInjection\Compiler\AddComponentDataResolver;

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
