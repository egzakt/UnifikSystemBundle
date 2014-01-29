<?php

namespace Unifik\SystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RouterExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // automatic routing parameters
        $container->setParameter('twig.extension.routing.class', 'Unifik\\SystemBundle\\Extensions\\RoutingExtension');
        $container->setParameter('router.options.generator_base_class', 'Unifik\\SystemBundle\\Lib\\RouterUrlGenerator');
        $container->findDefinition('twig.extension.routing')->addMethodCall('setAutoParametersHandler', array(
            new Reference('unifik_system.router_auto_parameters_handler')
        ));

        // i18n loader override
        $container->setParameter('jms_i18n_routing.loader.class', 'Unifik\\SystemBundle\\Routing\\Loader');
        $container->setParameter('jms_i18n_routing.route_exclusion_strategy.class', 'Unifik\\SystemBundle\\Routing\\RouteExclusionStrategy');
        $container->findDefinition('jms_i18n_routing.loader')->addMethodCall('setDatabaseConnection', array(
            new Reference('database_connection')
        ));

        // i18n pattern generation override
        $container->setParameter('jms_i18n_routing.pattern_generation_strategy.class', 'Unifik\\SystemBundle\\Routing\\PatternGenerationStrategy');
        $container->findDefinition('jms_i18n_routing.pattern_generation_strategy')->addArgument(new Reference('database_connection'));
    }
}
