<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * @deprecated
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
     * @return BaseEntityRepository
     */
    protected function getRepository($name)
    {
        return $this->getDoctrine()->getRepository($name);
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

    /**
     * Set a success message flash.
     *
     * @param string $message
     */
    protected function addFlashSuccess($message)
    {
        $this->addFlash(ApplicationController::FLASH_SUCCESS, $message);
    }

    /**
     * Set an error message flash.
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
     * Allow a redirection by passing a condition.
     * If the condition is true, the $ifTrue is used for redirection. Else it's $ifFalse.
     * $ifTrue or $ifFalse can be a string with the route name or an array containing the route name and params.
     * Exemple :
     *  $this->redirectIf($x == 1,
     *      array('egzakt_route_entity_action', array('id' => 1)),
     *      'egzakt_route_entity_default'
     *  );
     *
     * @param bool $condition
     * @param string|array $ifTrue
     * @param string|array $ifFalse
     * @return RedirectResponse
     */
    protected function redirectIf($condition, $ifTrue, $ifFalse)
    {
        $routeArgs = array();
        $routeName = null;

        if ($condition) {
            if ( is_array($ifTrue) ) {
                $routeName = $ifTrue[0];
                $routeArgs = $ifTrue[1];
            } else {
                $routeName = $ifTrue;
            }
        } else {
            if ( is_array($ifFalse) ) {
                $routeName = $ifFalse[0];
                $routeArgs = $ifFalse[1];
            } else {
                $routeName = $ifFalse;
            }
        }

        $this->redirectTo($routeName, $routeArgs);
    }

    /**
     * Return a redirect response.
     *
     * @param string $routeName
     * @param array $args
     * @return RedirectResponse
     */
    protected function redirectTo($routeName, $args = array())
    {
        return $this->redirect($this->generateUrl($routeName, $args));
    }

    /**
     * Return a parameter value provided by its name.
     * If a parameter doesn't exist, return the default value.
     *
     * @param string $name
     * @param mixed|null $default
     * @return mixed|null
     */
    protected function getParameter($name, $default = null)
    {
        return
            $this->container->hasParameter($name) ?
            $this->container->getParameter($name) :
            $default;
    }

}