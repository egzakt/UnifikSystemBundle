<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Egzakt\SystemBundle\Lib\ApplicationController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Base Controller for all Egzakt backend bundles
 */
abstract class BaseController extends ApplicationController
{


    /**
     * Return the core
     *
     * @return Core
     */
    public function getCore()
    {
        return $this->container->get('egzakt_backend.core');
    }

    /**
     * @inheritdoc
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate(
            $route,
            $this->get('egzakt_system.router_auto_parameters_handler')->inject($parameters),
            $referenceType
        );
    }


}
