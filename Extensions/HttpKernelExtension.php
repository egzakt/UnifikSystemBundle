<?php

namespace Egzakt\SystemBundle\Extensions;

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
     * @param $controller
     * @param array $attributes
     * @param array $query
     *
     * @return ControllerReference
     */
    public function controller($controller, $attributes = array(), $query = array())
    {
        $request = $this->container->get('request');

        // The rendered controller must be in the Egzakt namespace and the master request has to be egzakt enabled
        if (0 === strpos($controller, 'Egzakt') && $request->get('_egzaktEnabled')) {
            $attributes['_egzaktRequest'] = $request->get('_egzaktRequest');
            $attributes['_egzaktEnabled'] = true;

            // Automatically set the Master Request Locale to Sub-Requests
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
