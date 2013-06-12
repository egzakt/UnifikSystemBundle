<?php

namespace Egzakt\SystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

use Symfony\Component\DependencyInjection\Reference;

class RouterExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('twig.extension.routing.class', 'Egzakt\SystemBundle\Extensions\RoutingExtension');
        $container->setParameter('router.options.generator_base_class', 'Egzakt\SystemBundle\Lib\RouterUrlGenerator');

        // scope widening injection bypass, alternatively rely on the provider pattern by injecting the container itself
        $container->findDefinition('twig.extension.routing')->addMethodCall('setAutoParametersHandler', array(
            new Reference('egzakt_system.router_auto_parameters_handler')
        ));
    }
}
