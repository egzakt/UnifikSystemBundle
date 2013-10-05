<?php

namespace Flexy\SystemBundle\Lib;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Flexy\SystemBundle\Lib\EntityInterface;
use Flexy\SystemBundle\Lib\NavigationElementInterface;

/**
 * Flexy Backend Base for Entities
 */
abstract class BaseEntity implements EntityInterface, NavigationElementInterface
{
    /**
     * The element is currently selected.
     * Used in navigations.
     *
     * @var boolean
     */
    protected $selected;

    /**
     * The level of the element in the navigation
     *
     * @var integer
     */
    protected $level;

    /**
     * The parent element
     *
     * @var BaseEntity
     */
    protected $parent;

    /**
     * The container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The route name
     *
     * @var string
     */
    protected $route;

    /**
     * Returns the entity name without its path
     *
     * @return string
     */
    public function getEntityName()
    {
        $className = get_class($this);
        $classNameTokens = explode('\\', $className);

        return array_pop($classNameTokens);
    }

    /**
     * Gets the children
     *
     * @return array
     */
    public function getChildren()
    {
        return array();
    }

    /**
     * Return true if the entity has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return false;
    }

    /**
     * Gets the Parent
     *
     * @return bool
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the Parent
     *
     * @param object $parent The parent object
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns true if selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * Sets the selected state
     *
     * @param boolean $bool The selected state
     */
    public function setSelected($bool)
    {
        $this->selected = $bool;
    }

    /**
     * Returns true if active
     *
     * @return bool
     */
    public function isActive()
    {
        return false;
    }

    /**
     * Returns true if editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * Returns true if deletable
     *
     * @return bool
     */
    public function isDeletable()
    {
        if (!$this->getId()) {
            return false;
        }

        if (method_exists($this, 'getDeleteRestrictions')) {
            foreach ($this->getDeleteRestrictions() as $method) {

                $result = $this->$method();

                if ((is_bool($result) && $result == true) || (!is_bool($result) && count($result))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get Level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set Level
     *
     * @param integer $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Sets the Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Gets the System Core
     *
     * @return \Flexy\System\CoreBundle\Lib\Core
     */
    protected function getSystemCore()
    {
        return $this->container->get('flexy_system.core');
    }

    /**
     * Gets the current application core
     *
     * @return \Flexy\Backend\CoreBundle\Lib\Core
     */
    public function getCore()
    {
        return $this->container->get('flexy_' . $this->getSystemCore()->getCurrentAppName() . '.core');
    }

    /**
     * Gets the Route of the entity
     *
     * @param string $suffix The suffix to be concatenated after the Route
     *
     * @return string
     */
    public function getRoute($suffix = '')
    {
        if ($this->route) {
            return $this->route;
        }

        $currentAppName = ucfirst($this->getSystemCore()->getCurrentAppName());
        $methodName = 'getRoute' . $currentAppName;

        // Fallback to frontend method if the current app does not define any method
        if (false == method_exists($this, $methodName) && 'Backend' !== $currentAppName) {
            $methodName = 'getRouteFrontend';
        }

        if ($suffix) {
            $route = $this->$methodName($suffix);
        } else {
            $route = $this->$methodName();
        }

        return $route;
    }

    /**
     * Get the Route Params
     *
     * @param array $params Params to get
     *
     * @return array
     */
    public function getRouteParams($params = array())
    {
        $currentAppName = ucfirst($this->getSystemCore()->getCurrentAppName());
        $methodName = 'getRoute' . $currentAppName . 'Params';

        // Fallback to frontend method if the current app does not define any method
        if (false == method_exists($this, $methodName) && 'Backend' !== $currentAppName) {
            $methodName = 'getRouteFrontendParams';
        }

        $params = $this->$methodName($params);

        return $params;
    }

}
