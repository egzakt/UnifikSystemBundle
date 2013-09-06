<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * SectionNavigation
 */
class SectionNavigation extends BaseEntity
{

    /**
     * @var integer
     */
    private $ordering;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var Navigation
     */
    private $navigation;

    /**
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New section-navigation relation';
        }

        if ($this->section && $this->navigation) {
            return $this->section . ' - ' . $this->navigation;
        }

        return '';
    }

    /**
     * Set ordering
     *
     * @param integer $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
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
     * Set created at
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set section
     *
     * @param Section $section Section object
     */
    public function setSection(Section $section)
    {
        $this->section = $section;
    }

    /**
     * Get section
     *
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set navigation
     *
     * @param Navigation $navigation Navigation object
     */
    public function setNavigation(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * Get navigation
     *
     * @return Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }
}
