<?php

namespace Flexy\SystemBundle\Extensions;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

use Symfony\Bridge\Twig\Extension\HttpKernelExtension as BaseHttpKernelExtension;

class HttpKernelExtension extends BaseHttpKernelExtension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The goal of this method is to pass automatically certain master request attributes to subrequests.
     *
     * This fixes symfony bug #6932 (https://github.com/symfony/symfony/issues/6932)
     *
     * @param $controller
     * @param array $attributes
     * @param array $query
     *
     * @return ControllerReference
     */
    public function controller($controller, $attributes = array(), $query = array())
    {
        $request = $this->container->get('request');

        // The rendered controller must be in the Flexy namespace and the master request has to be flexy enabled
        if (0 === strpos($controller, 'Flexy') && $request->get('_flexyEnabled')) {
            $attributes['_flexyRequest'] = $request->get('_flexyRequest');
            $attributes['_flexyEnabled'] = true;
            $attributes['_masterRoute'] = $request->get('_masterRoute', $request->get('_route')); // chained to support multiples embedded subrequests
            $attributes['_locale'] = $request->getLocale();
        }

        return parent::controller($controller, $attributes, $query);
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}
