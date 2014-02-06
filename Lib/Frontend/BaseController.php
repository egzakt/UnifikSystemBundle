<?php

namespace Unifik\SystemBundle\Lib\Frontend;

use Unifik\SystemBundle\Lib\ApplicationController;
use Unifik\SystemBundle\Lib\NavigationElement;

/**
 * Base Controller for all Unifik Frontend Bundles
 */
abstract class BaseController extends ApplicationController
{
    /**
     * Helper method to create a navigation element
     *
     * @param string $name
     * @param string $route
     * @param array  $routeParams
     *
     * @return NavigationElement
     */
    protected function createNavigationElement($name, $route, $routeParams = array())
    {
        $navigationElement = new NavigationElement();
        $navigationElement->setContainer($this->container);
        $navigationElement->setName($name);
        $navigationElement->setRouteFrontend($route);
        $navigationElement->setRouteFrontendParams($routeParams);

        return $navigationElement;
    }

    /**
     * Get the frontend core
     *
     * @return Core
     */
    public function getCore()
    {
        return $this->get('unifik_frontend.core');
    }


}
