<?php

namespace Egzakt\SystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

use Symfony\Component\DependencyInjection\Reference;

class RouterExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // automatic routing parameters
        $container->setParameter('twig.extension.routing.class', 'Egzakt\\SystemBundle\\Extensions\\RoutingExtension');
        $container->setParameter('router.options.generator_base_class', 'Egzakt\\SystemBundle\\Lib\\RouterUrlGenerator');
        $container->findDefinition('twig.extension.routing')->addMethodCall('setAutoParametersHandler', array(
            new Reference('egzakt_system.router_auto_parameters_handler')
        ));

        // i18n loader override
        $container->setParameter('jms_i18n_routing.loader.class', 'Egzakt\\SystemBundle\\Routing\\Loader');
        $container->setParameter('jms_i18n_routing.route_exclusion_strategy.class', 'Egzakt\\SystemBundle\\Routing\\RouteExclusionStrategy');
        $container->findDefinition('jms_i18n_routing.loader')->addMethodCall('setDatabaseConnection', array(
            new Reference('database_connection')
        ));
    }
}
