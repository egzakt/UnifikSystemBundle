<?php

namespace Flexy\SystemBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RouterAutoParametersHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @return array
     */
    public function getAutoParameters()
    {
        $request = $this->container->get('request');

        $flexyRequest = $request->get('_flexyRequest');

        $sectionId = $flexyRequest['sectionId'];

        $parameters = array(
            'sectionId' => $sectionId
        );

        return $parameters;
    }

    /**
     * This inject auto parameters into a given parameter array
     *
     * @param array $parameters
     *
     * @return array
     */
    public function inject($parameters)
    {
        $parameters = array_merge($this->getAutoParameters(), $parameters);

        return $parameters;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}
