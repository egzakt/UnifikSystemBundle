<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Egzakt\SystemBundle\Entity\Section;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @return \Egzakt\Backend\CoreBundle\Lib\Core
     */
    public function getBackendCore()
    {
        return $this->getCore();
    }

    /**
     * Return the system core
     *
     * @return \Egzakt\System\CoreBundle\Lib\Core
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
     * Get the SectionBundle entity
     *
     * @return \Egzakt\Backend\SectionBundle\Entity\SectionBundle
     */
    public function getSectionBundle()
    {
        return $this->getCore()->getSectionBundle();
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
     * Get the Bundle
     *
     * @return \Egzakt\Backend\CoreBundle\Entity\Bundle
     */
    public function getBundle()
    {
        return $this->getCore()->getBundle();
    }

    /**
     * Get the App
     *
     * @return \Egzakt\Backend\CoreBundle\Entity\App
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
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Add a custom navigation element for the current tab
     *
     * @param string $tabIndex Index of the tab in the configuration array
     * @param Entity $entity
     * @param array  $tabs List of tabs
     */
    protected function addTabNavigationElement($tabIndex, $entity, $tabs = null)
    {
        if (!$tabs) {
            $tabs = $this->getSectionBundle()->getParam('tabs');
        }

        $navigationElement = new NavigationElement();
        $navigationElement->setContainer($this->get('service_container'));
        $navigationElement->setName($tabs[$tabIndex]['name']);
        $navigationElement->setRouteBackend($entity->getRoute($tabs[$tabIndex]['route_suffix']));
        $navigationElement->setRouteBackendParams($entity->getRouteParams(array('id' => $entity->getId())));

        $this->getCore()->addNavigationElement($navigationElement);
    }

    protected function createNavigationElement($name, $route, $routeParams)
    {
        $navigationElement = new NavigationElement();
        $navigationElement->setContainer($this->container);
        $navigationElement->setName($name);
        $navigationElement->setRouteBackend($route);
        $navigationElement->setRouteBackendParams($routeParams);

        return $navigationElement;
    }

    protected function addNavigationElement($element)
    {
        trigger_error('addNavigationElement is deprecated. Use pushNavigationElement instead.', E_USER_DEPRECATED);

        $this->pushNavigationElement($element);
    }

    protected function pushNavigationElement($element)
    {
        $this->getCore()->addNavigationElement($element);
    }

    protected function createAndPushNavigationElement($name, $route, $routeParams)
    {
        $navigationElement = $this->createNavigationElement($name, $route, $routeParams);
        $this->pushNavigationElement($navigationElement);
    }
}
