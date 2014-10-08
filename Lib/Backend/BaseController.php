<?php

namespace Unifik\SystemBundle\Lib\Backend;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Unifik\SystemBundle\Lib\ApplicationController;
use Unifik\SystemBundle\Lib\NavigationElement;

/**
 * Base Controller for all Unifik backend bundles
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
        return $this->container->get('unifik_backend.core');
    }

    /**
     * Inits a new Entity with default values
     *
     * @TODO This method will be renamed to initTranslatableEntity once we get rid of the container.
     *
     * @param $entity
     *
     * @return mixed
     */
    protected function initEntity($entity)
    {
        // Set the Edit Locale on translatable entities
        if (method_exists($entity, 'setCurrentLocale')) {
            $entity->setCurrentLocale($this->container->get('unifik_backend.core')->getEditLocale());
        }

        // @TODO Remove the container from entities
        $entity->setContainer($this->container);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate(
            $route,
            $this->get('unifik_system.router_auto_parameters_handler')->inject($parameters),
            $referenceType
        );
    }

    /**
     * @inheritdoc
     */
    public function forward($controller, array $path = array(), array $query = array())
    {
        $path['_controller'] = $controller;

        $currentRequest = $this->container->get('request_stack')->getCurrentRequest();
        $subRequest = $currentRequest->duplicate($query, null, $path);

        $unifikAttributes = [
            '_unifikEnabled' => $currentRequest->attributes->get('_unifikEnabled', false),
            '_unifikRequest' => $currentRequest->attributes->get('_unifikRequest')
        ];

        $subRequest->attributes->add($unifikAttributes);

        return $this->container->get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

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
        $navigationElement->setRouteBackend($route);
        $navigationElement->setRouteBackendParams($routeParams);

        return $navigationElement;
    }
}
