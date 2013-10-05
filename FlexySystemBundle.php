<?php

namespace Flexy\SystemBundle;

use Flexy\SystemBundle\DependencyInjection\Compiler\DeletableExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Flexy\SystemBundle\DependencyInjection\Compiler\RouterExtensionCompilerPass;
use Flexy\SystemBundle\DependencyInjection\Compiler\HttpKernelExtensionCompilerPass;

class FlexySystemBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RouterExtensionCompilerPass());
        $container->addCompilerPass(new HttpKernelExtensionCompilerPass());
        $container->addCompilerPass(new DeletableExtensionCompilerPass());
    }
}
