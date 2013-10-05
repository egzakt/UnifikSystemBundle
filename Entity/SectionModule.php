<?php

namespace Flexy\SystemBundle\Entity;

/**
 * SectionModule
 */
class SectionModule
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var integer
     */
    private $ordering;

    /**
     * @var \Flexy\SystemBundle\Entity\Section
     */
    private $section;

    /**
     * @var \Flexy\SystemBundle\Entity\App
     */
    private $app;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set controller
     *
     * @param  string        $controller
     * @return SectionModule
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set ordering
     *
     * @param  integer       $ordering
     * @return SectionModule
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return integer
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set section
     *
     * @param  \Flexy\SystemBundle\Entity\Section $section
     * @return SectionModule
     */
    public function setSection(\Flexy\SystemBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \Flexy\SystemBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set app
     *
     * @param  \Flexy\SystemBundle\Entity\App $app
     * @return SectionModule
     */
    public function setApp(\Flexy\SystemBundle\Entity\App $app = null)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return \Flexy\SystemBundle\Entity\App
     */
    public function getApp()
    {
        return $this->app;
    }
    /**
     * @var \Flexy\SystemBundle\Entity\Navigation
     */
    private $navigation;

    /**
     * Set navigation
     *
     * @param  \Flexy\SystemBundle\Entity\Navigation $navigation
     * @return SectionModule
     */
    public function setNavigation(\Flexy\SystemBundle\Entity\Navigation $navigation = null)
    {
        $this->navigation = $navigation;

        return $this;
    }

    /**
     * Get navigation
     *
     * @return \Flexy\SystemBundle\Entity\Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }
    /**
     * @var string
     */
    private $target;

    /**
     * Set target
     *
     * @param  string        $target
     * @return SectionModule
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
    /**
     * @var string
     */
    private $type;

    /**
     * Set type
     *
     * @param  string        $type
     * @return SectionModule
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
