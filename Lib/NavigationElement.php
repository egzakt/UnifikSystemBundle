<?php

namespace Flexy\SystemBundle\Lib;

use Flexy\SystemBundle\Lib\BaseEntity;

/**
 * Navigation Element
 */
class NavigationElement extends BaseEntity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $routeBackend;

    /**
     * @var array
     */
    private $routeBackendParams;

    /**
     * @var string
     */
    private $routeFrontend;

    /**
     * @var array
     */
    private $routeFrontendParams;

    /**
     * @var \Datetime
     */
    private $updatedAt;

    /**
     * Return a string representing the Element
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name Name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Route Backend
     *
     * @param string $routeBackend Route Backend
     */
    public function setRouteBackend($routeBackend)
    {
        $this->routeBackend = $routeBackend;
    }

    /**
     * Set Route Backend Params
     *
     * @param array $routeBackendParams Route Backend Params
     */
    public function setRouteBackendParams($routeBackendParams)
    {
        $this->routeBackendParams = $routeBackendParams;
    }

    /**
     * Get Route Backend
     *
     * @return string
     */
    public function getRouteBackend()
    {
        return $this->routeBackend;
    }

    /**
     * Get Route Backend Params
     *
     * @param array $params
     *
     * @return array
     */
    public function getRouteBackendParams($params = array())
    {
        if ($this->routeBackendParams) {
            return $this->routeBackendParams;
        }

        return $params;
    }

    /**
     * Set Route Frontend
     *
     * @param string $routeFrontend Route Frontend
     */
    public function setRouteFrontend($routeFrontend)
    {
        $this->routeFrontend = $routeFrontend;
    }

    /**
     * Set Route Frontend Params
     *
     * @param array $routeFrontendParams Route Frontend Params
     */
    public function setRouteFrontendParams($routeFrontendParams)
    {
        $this->routeFrontendParams = $routeFrontendParams;
    }

    /**
     * Get Route Frontend
     *
     * @return string
     */
    public function getRouteFrontend()
    {
        return $this->routeFrontend;
    }

    /**
     * Get Route Frontend Params
     *
     * @param array $params
     *
     * @return array
     */
    public function getRouteFrontendParams($params = array())
    {
        if ($this->routeFrontendParams) {
            return $this->routeFrontendParams;
        }

        return $params;
    }

    /**
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
