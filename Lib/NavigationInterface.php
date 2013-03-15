<?php

namespace Egzakt\SystemBundle\Lib;

/**
 * Navigation Interface
 */
interface NavigationInterface
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
    function setlevel($level);
}