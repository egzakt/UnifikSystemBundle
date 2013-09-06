<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Finder\Finder;

class RouterInvalidator
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;


    public function __construct(Kernel $kernel, Finder $finder)
    {
        $this->kernel = $kernel;
        $this->finder = $finder;
    }

    /**
     * Invalidate the routing cache
     */
    public function invalidate()
    {
        foreach ($this->getFiles('/app' . $this->getKernelEnvironment() . 'Url(Matcher|Generator)(.*)/i')->in($this->getKernelCacheDir()) as $file) {
            unlink($file);
        }
    }

    /**
     * Return the kernel environment.
     * @return string
     */
    protected function getKernelEnvironment()
    {
        return $this->kernel->getEnvironment();
    }

    /**
     * Return the cache directory.
     * @return string
     */
    protected function getKernelCacheDir()
    {
        return $this->kernel->getCacheDir();
    }

    /**
     * @param $regex
     * @return Finder
     */
    protected function getFiles($regex)
    {
        return $this->finder->files()->name($regex);
    }

}
