<?php

namespace Unifik\SystemBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class DeletableExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('unifik_system.deletable')) {
            return;
        }

        $definition = $container->getDefinition('unifik_system.deletable');
        $taggedServices = $container->findTaggedServiceIds('unifik_system.deletable');

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addListener', array(new Reference($id), $attributes[0]['entity']));
        }

    }
}
