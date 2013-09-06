<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Finder\Finder;

class RouterInvalidator
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @param Kernel $kernel
     */
    public function __construct($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Invalidate the routing cache
     */
    public function invalidate()
    {
        $finder = new Finder();
        $cacheDir = $this->kernel->getCacheDir();

        foreach ($finder->files()->name('/app' . $this->kernel->getEnvironment() . 'Url(Matcher|Generator)(.*)/i')->in($cacheDir) as $file) {
            unlink($file);
        }
    }
}
