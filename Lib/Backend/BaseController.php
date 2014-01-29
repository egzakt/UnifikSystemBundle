<?php

namespace Unifik\SystemBundle\Lib\Backend;

use Unifik\SystemBundle\Lib\ApplicationController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
}
