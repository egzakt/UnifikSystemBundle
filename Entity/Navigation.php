<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Navigation
 */
class Navigation extends BaseEntity
{
    /**
     * @var integer $id
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
}