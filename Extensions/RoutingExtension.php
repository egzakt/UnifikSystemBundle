<?php

namespace Flexy\SystemBundle\Extensions;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bridge\Twig\Extension\RoutingExtension as BaseRoutingExtension;

use Flexy\SystemBundle\Lib\RouterAutoParametersHandler;

class RoutingExtension extends BaseRoutingExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var RouterAutoParametersHandler
     */
    private $autoParametersHandler;

    /**
     * @param RouterAutoParametersHandler $autoParametersHandler
     */
    public function setAutoParametersHandler($autoParametersHandler)
    {
        $this->autoParametersHandler = $autoParametersHandler;
    }

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
        $parameters = $this->autoParametersHandler->inject($parameters);

        return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Overriden to handle automatics parameters.
     *
     * @inheritdoc
     */
    public function getUrl($name, $parameters = array(), $schemeRelative = false)
    {
        $parameters = $this->autoParametersHandler->inject($parameters);

        return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
