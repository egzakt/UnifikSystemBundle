<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Finder\Finder;

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

        $sectionId = $request->get('sectionId',
            $request->get('section_id', 0) // backward compatibility double-check
        );

        if (false == $sectionId && $section = $request->get('section')) {
            $sectionId = $section->getId();
        }

        $parameters = array(
            'section_id' => $sectionId,
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