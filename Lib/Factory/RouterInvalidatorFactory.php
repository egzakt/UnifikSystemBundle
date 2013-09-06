<?php

namespace Egzakt\SystemBundle\Lib\Factory;

use Egzakt\SystemBundle\Lib\RouterInvalidator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class RouterInvalidatorFactory
 * @package Egzakt\SystemBundle\Lib\Factory
 */
class RouterInvalidatorFactory
{

    /**
     * Create the RouterInvalidator object
     * @param Kernel $kernel
     * @return RouterInvalidator
     */
    public function create(Kernel $kernel)
    {

        $finder = new Finder();
        $invalidator = new RouterInvalidator($kernel, $finder);

        return $invalidator;
    }

}