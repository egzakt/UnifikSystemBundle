<?php

namespace Egzakt\SystemBundle;

use Egzakt\SystemBundle\DependencyInjection\Compiler\DeletableExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Egzakt\SystemBundle\DependencyInjection\Compiler\RouterExtensionCompilerPass;
use Egzakt\SystemBundle\DependencyInjection\Compiler\HttpKernelExtensionCompilerPass;

class EgzaktSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RouterExtensionCompilerPass());
        $container->addCompilerPass(new HttpKernelExtensionCompilerPass());
        $container->addCompilerPass(new DeletableExtensionCompilerPass());
    }
}
