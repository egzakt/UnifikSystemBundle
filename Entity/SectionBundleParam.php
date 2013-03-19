<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section Bundle Param
 */
class SectionBundleParam
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
     * @var string $value
     */
    private $value;

    /**
     * @var SectionBundle
     */
    private $sectionBundle;

    /**
     * Set name
     *
     * @param string $name Name
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value Value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set sectionBundle
     *
     * @param SectionBundle $sectionBundle The SectionBundle object
     */
    public function setSectionBundle(SectionBundle $sectionBundle)
    {
        $this->sectionBundle = $sectionBundle;
    }

    /**
     * Get sectionBundle
     *
     * @return SectionBundle
     */
    public function getSectionBundle()
    {
        return $this->sectionBundle;
    }

}