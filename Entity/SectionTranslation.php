<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

/**
 * SectionTranslation
 */
class SectionTranslation
{
    use EgzaktORMBehaviors\Translatable\Translation;

    use EgzaktORMBehaviors\Sluggable\Sluggable;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $pageTitle
     */
    private $pageTitle;

    /**
     * @var string $headCode
     */
    private $headCode;

    /**
     * @var boolean $active
     */
    private $active;

    /**
     * @return int
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
     * Set pageTitle
     *
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Get pageTitle
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * Set headCode
     *
     * @param string $headCode
     */
    public function setHeadCode($headCode)
    {
        $this->headCode = $headCode;
    }

    /**
     * Get headCode
     *
     * @return string
     */
    public function getHeadCode()
    {
        return $this->headCode;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get Sluggable Fields
     *
     * @return array
     */
    public function getSluggableFields()
    {
        return array('name');
    }

}
