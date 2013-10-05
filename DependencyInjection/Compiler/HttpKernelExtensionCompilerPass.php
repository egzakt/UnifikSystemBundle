<?php

namespace Flexy\SystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

use Symfony\Component\DependencyInjection\Reference;

class HttpKernelExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setParameter('twig.extension.httpkernel.class', 'Flexy\\SystemBundle\\Extensions\\HttpKernelExtension');
        $container->findDefinition('twig.extension.httpkernel')->addMethodCall('setContainer', array(
            new Reference('service_container')
        ));
    }
}
