<?php

namespace Egzakt\SystemBundle\Extensions;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bridge\Twig\Extension\RoutingExtension as BaseRoutingExtension;

class RoutingExtension extends BaseRoutingExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Overriden to handle automatics parameters.
     *
     * @inheritdoc
     */
    public function getPath($name, $parameters = array(), $relative = false)
    {
        $parameters = $this->pushAutoParameters($parameters);

        return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Overriden to handle automatics parameters.
     *
     * @inheritdoc
     */
    public function getUrl($name, $parameters = array(), $schemeRelative = false)
    {
        $parameters = $this->pushAutoParameters($parameters);

        return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritdoc
     */
    private function pushAutoParameters($parameters)
    {
        $sectionId = $this->container->get('request')->get(
            'sectionId', $this->container->get('request')->get('section_id', 0) // backward compatibility double-check
        );

        $autoParameters = array(
            'section_id' => $sectionId,
            'sectionId' => $sectionId
        );

        $parameters = array_merge($autoParameters, $parameters);

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