<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\App;

use Doctrine\ORM\EntityManager;

abstract class ApplicationController extends Controller implements BaseControllerInterface
{

    const FLASH_SUCCESS = 'success';
    const FLASH_ERROR = 'error';

    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * Return the core.
     *
     * @return ApplicationCoreInterface
     */
    abstract public function getCore();

    /**
     * @return Core
     */
    protected function getSystemCore()
    {
        return $this->get('egzakt_system.core');
    }

    /**
     * Get the current section entity
     *
     * @return Section
     */
    protected function getSection()
    {
        return $this->getCore()->getSection();
    }


    /**
     * Get the current app entity
     *
     * @return App
     */
    protected function getApp()
    {
        return $this->getCore()->getApp();
    }

    /**
     * Get the current app name
     *
     * @return string
     */
    protected function getAppName()
    {
        return $this->getSystemCore()->getCurrentAppName();
    }

    /**
     * Get the Entity Manager
     *
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Return the repository of an entity.
     *
     * @param $name
     * @return EntityRepository
     */
    protected function getRepository($name)
    {
        return $this->getDoctrine()->getRepository($name);
    }

    /**
     * Set a flash message for a given type.
     *
     * @param string $type
     * @param string $message
     */
    protected function setFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->set($type, $message);
    }

    /**
     * Add a flash message in the corresponding type array.
     *
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * Has flash messages for a given type?
     *
     * @param string $type
     *
     * @return boolean
     */
    protected function hasFlash($type)
    {
        return $this->get('session')->getFlashBag()->has($type);
    }

    /**
     * Gets and clears flash from the stack.
     *
     * @param string $type
     * @param array  $default Default value if $type does not exist.
     *
     * @return array
     */
    protected function getFlash($type, array $default = array())
    {
        return $this->get('session')->getFlashBag()->get($type, $default);
    }

    /**
     * Set the success flash message.
     *
     * @param string $message
     */
    protected function setFlashSuccess($message)
    {
        $this->setFlash(ApplicationController::FLASH_SUCCESS, $message);
    }

    /**
     * Add a flash message in the success array.
     *
     * @param string $message
     */
    protected function addFlashSuccess($message)
    {
        $this->addFlash(ApplicationController::FLASH_SUCCESS, $message);
    }

    /**
     * Add a flash message in the error array.
     *
     * @param string $message
     */
    protected function addFlashError($message)
    {
        $this->addFlash(ApplicationController::FLASH_ERROR, $message);
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
     * Check is an entity can be deleted.
     * The entity is sent to our deletable service.
     * The service will return an object which can be "fail" or "deletable".
     *
     * @param  Object          $entity
     * @return DeletableResult
     */
    protected function checkDeletable($entity)
    {
        $ds = $this->get('egzakt_system.deletable');
        return $ds->checkDeletable($entity);
    }

}