<?php

namespace Egzakt\SystemBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Egzakt\SystemBundle\DependencyInjection\Compiler\RouterExtensionCompilerPass;

class EgzaktSystemBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RouterExtensionCompilerPass());
    }
}
