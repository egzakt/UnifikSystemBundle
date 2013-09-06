<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Egzakt\SystemBundle\Entity\App;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Lib\BaseControllerInterface;
use Egzakt\SystemBundle\Lib\NavigationElement;

/**
 * Base Controller for all Egzakt backend bundles
 */
abstract class BaseController extends Controller implements BaseControllerInterface
{
    /**
     * Init
     */
    public function init()
    {
        // base implementation
    }

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
     *
     * @return BackendCore
     */
    public function getBackendCore()
    {
        return $this->getCore();
    }

    /**
     * Return the system core
     *
     * @return Core
     */
    public function getSystemCore()
    {
        return $this->container->get('egzakt_system.core');
    }

    /**
     * Get the Section entity
     *
     * @return Section
     */
    public function getSection()
    {
        return $this->getCore()->getSection();
    }

    /**
     * Get the Bundle Name
     *
     * @return string
     */
    public function getBundleName()
    {
        trigger_error('getBundleName is deprecated.', E_USER_DEPRECATED);

        return $this->getCore()->getBundleName();
    }

    /**
     * Get the current app entity
     *
     * @return App
     */
    public function getApp()
    {
        return $this->getCore()->getApp();
    }

    /**
     * Get the current app name
     *
     * @return string
     */
    public function getCurrentAppName()
    {
        return $this->getSystemCore()->getCurrentAppName();
    }

    /**
     * Get the Entity Manager
     *
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->getDoctrine()->getManager();
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
     *
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

    /**
     * Adds a flash message for type.
     *
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }
}
