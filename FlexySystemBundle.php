<?php

namespace Unifik\SystemBundle;

use Unifik\SystemBundle\DependencyInjection\Compiler\DeletableExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Unifik\SystemBundle\DependencyInjection\Compiler\RouterExtensionCompilerPass;
use Unifik\SystemBundle\DependencyInjection\Compiler\HttpKernelExtensionCompilerPass;

class UnifikSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RouterExtensionCompilerPass());
        $container->addCompilerPass(new HttpKernelExtensionCompilerPass());
        $container->addCompilerPass(new DeletableExtensionCompilerPass());
    }
}
