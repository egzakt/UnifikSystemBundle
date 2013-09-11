<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Egzakt\SystemBundle\Lib\ApplicationController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Egzakt\SystemBundle\Lib\NavigationElement;

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
     * Return the Backend Core.
     *
     * @deprecated Use getCore.
     * @return BackendCore
     */
    public function getBackendCore()
    {
        return $this->getCore();
    }


    /**
     * Get the Bundle Name
     * @deprecated
     * @return string
     */
    public function getBundleName()
    {
        trigger_error('getBundleName is deprecated.', E_USER_DEPRECATED);
        return $this->getCore()->getBundleName();
    }

    /**
     * Helper method to create a navigation element
     *
     * @param $name
     * @param $route
     * @param array $routeParams
     *
     * @return NavigationElement
     */
    protected function createNavigationElement($name, $route, $routeParams = array())
    {
        $navigationElement = new NavigationElement();
        $navigationElement->setContainer($this->container);
        $navigationElement->setName($name);
        $navigationElement->setRouteBackend($route);
        $navigationElement->setRouteBackendParams($routeParams);

        return $navigationElement;
    }

    /**
     * Push a navigation element on top on the navigation element stack
     *
     * @param $element
     * @deprecated Use pushNavigationElement instead
     */
    protected function addNavigationElement($element)
    {
        trigger_error('addNavigationElement is deprecated. Use pushNavigationElement instead.', E_USER_DEPRECATED);

        $this->pushNavigationElement($element);
    }

    /**
     * Push a navigation element on top on the navigation element stack.
     *
     * @param $element
     */
    protected function pushNavigationElement($element)
    {
        $this->getCore()->addNavigationElement($element);
    }

    /**
     * Helper method to create and push a navigation element to the navigation stack.
     *
     * @param $name
     * @param $route
     * @param array $routeParams
     */
    protected function createAndPushNavigationElement($name, $route, $routeParams = array())
    {
        $navigationElement = $this->createNavigationElement($name, $route, $routeParams);
        $this->pushNavigationElement($navigationElement);
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
