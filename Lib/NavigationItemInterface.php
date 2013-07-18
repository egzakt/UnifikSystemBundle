<?php

namespace Egzakt\SystemBundle\Lib;

/**
 * Navigation Item Interface
 */
interface NavigationItemInterface
{
    /**
     * isSelected
     *
     * @abstract
     */
    function isSelected();

    /**
     * setSelected
     *
     * @param boolean $bool Selected State
     *
     * @abstract
     */
    function setSelected($bool);

    /**
     * Get Children
     *
     * @abstract
     */
    function getChildren();

    /**
     * Has Children
     *
     * @abstract
     */
    function hasChildren();

    /**
     * Get Parent
     *
     * @abstract
     */
    function getParent();

    /**
     * Set Parent
     *
     * @param object $parent The Parent
     *
     * @abstract
     */
    function setParent($parent);

    /**
     * Get Level
     *
     * @abstract
     */
    function getLevel();

    /**
     * Set Level
     *
     * @param integer $level The Level
     *
     * @abstract
     */
    function setLevel($level);

    /**
     * Get Entity
     *
     * @abstract
     */
    function getEntity();

    /**
     * Set Entity
     *
     * @param $entity
     *
     * @abstract
     */
    function setEntity($entity);
}