<?php

namespace Egzakt\SystemBundle\Lib;

use Egzakt\SystemBundle\Lib\BaseEntity;
use Egzakt\SystemBundle\Lib\NavigationInterface;

class NavigationItem implements NavigationInterface
{
    /**
     * @var BaseEntity $entity
     */
    protected $entity;

    /**
     * @var array $children
     */
    protected $children;

    /**
     * @var mixed $parent
     */
    protected $parent;

    /**
     * @var bool $selected
     */
    protected $selected;

    /**
     * @var integer $level
     */
    protected $level;

    /**
     * @var mixed $sectionHooks
     */
    protected $sectionHooks;


    public function __construct()
    {
        $this->children = array();
        $this->sectionHooks = array();
    }

    public function __toString()
    {
        return $this->entity->__toString();
    }

    /**
     * Set Entity
     *
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get Entity
     *
     * @return BaseEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set Children
     *
     * @param $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Get Children
     *
     * Merges the Childrens and the Section Hooks
     *
     * @return mixed
     */
    public function getChildren()
    {
        return array_merge($this->children, $this->sectionHooks);
    }

    /**
     * Add Children
     *
     * @param $children
     */
    public function addChildren($children)
    {
        $this->children[] = $children;
    }

    /**
     * Had Children
     *
     * @return int
     */
    public function hasChildren()
    {
        return count($this->getChildren());
    }

    /**
     * Remove Children
     *
     * @param $object
     */
    public function removeChildren($object)
    {
        foreach($this->children as $key => $children) {
            if ($children == $object) {
                unset($this->children[$key]);
            }
        }
    }

    /**
     * Set Parent
     *
     * @param $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get Parent
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set Selected
     *
     * @param boolean $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * Get Selected
     *
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Is Selected
     *
     * @return bool
     */
    function isSelected()
    {
        return $this->selected;
    }

    /**
     * Get Level
     *
     * @return integer
     */
    function getLevel()
    {
        return $this->level;
    }

    /**
     * Set Level
     *
     * @param integer $level The Level
     */
    function setlevel($level)
    {
        $this->level = $level;
    }

    /**
     * Set Section Hooks
     *
     * @param mixed $sectionHooks
     */
    public function setSectionHooks($sectionHooks)
    {
        $this->sectionHooks = $sectionHooks;
    }

    /**
     * Get Section Hooks
     *
     * @return mixed
     */
    public function getSectionHooks()
    {
        return $this->sectionHooks;
    }

    /**
     * Gets the Route of the entity
     *
     * Backward Compatibility function, should not be used
     *
     * @param string $suffix The suffix to be concatenated after the Route
     *
     * @return string
     */
    public function getRoute($suffix = '')
    {
        return $this->entity->getRoute($suffix);
    }

    /**
     * Get the Route Params
     *
     * Backward Compatibility function, should not be used
     *
     * @param array $params Params to get
     *
     * @return array
     */
    public function getRouteParams($params = array())
    {
        return $this->entity->getRouteParams($params);
    }
}
