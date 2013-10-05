<?php

namespace Flexy\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Flexy\SystemBundle\Lib\BaseEntity;

/**
 * Navigation
 */
class Navigation extends BaseEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var SectionNavigation $sectionNavigations
     */
    private $sectionNavigations;

    /**
     * @var ArrayCollection $sections
     */
    private $sections;

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
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Construct
     */
    public function __construct()
    {
        $this->sectionNavigations = new ArrayCollection();
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get sectionNavigations
     *
     * @return ArrayCollection
     */
    public function getSectionNavigations()
    {
        return $this->sectionNavigations;
    }

    /**
     * Add sectionNavigation
     *
     * @param SectionNavigation $sectionNavigation
     */
    public function addSectionNavigation(SectionNavigation $sectionNavigation)
    {
        $this->sectionNavigations[] = $sectionNavigation;
    }

    /**
     * Get sections
     *
     * @return ArrayCollection
     */
    public function getSections()
    {
        if (!$this->sections) {

            $this->sections = new ArrayCollection();

            foreach ($this->sectionNavigations as $sectionNavigation) {
                $this->sections[] = $sectionNavigation->getSection();
            }
        }

        return $this->sections;
    }

    /**
     * Remove sectionNavigations
     *
     * @param \Flexy\SystemBundle\Entity\SectionNavigation $sectionNavigations
     */
    public function removeSectionNavigation(\Flexy\SystemBundle\Entity\SectionNavigation $sectionNavigations)
    {
        $this->sectionNavigations->removeElement($sectionNavigations);
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $mappings;

    /**
     * Add mappings
     *
     * @param  \Flexy\SystemBundle\Entity\Mapping $mappings
     * @return Navigation
     */
    public function addMapping(\Flexy\SystemBundle\Entity\Mapping $mappings)
    {
        $this->mappings[] = $mappings;

        return $this;
    }

    /**
     * Remove mappings
     *
     * @param \Flexy\SystemBundle\Entity\Mapping $mappings
     */
    public function removeMapping(\Flexy\SystemBundle\Entity\Mapping $mappings)
    {
        $this->mappings->removeElement($mappings);
    }

    /**
     * Get mappings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMappings()
    {
        return $this->mappings;
    }
    /**
     * @var \Flexy\SystemBundle\Entity\App
     */
    private $app;

    /**
     * Set app
     *
     * @param  \Flexy\SystemBundle\Entity\App $app
     * @return Navigation
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
     * @var string
     */
    private $code;

    /**
     * Set code
     *
     * @param  string     $code
     * @return Navigation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

}
