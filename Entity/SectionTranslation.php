<?php

namespace Egzakt\SystemBundle\Entity;

use Gedmo\Sluggable\Util\Urlizer;

use Egzakt\SystemBundle\Lib\BaseTranslationEntity;

/**
 * SectionTranslation
 */
class SectionTranslation extends BaseTranslationEntity
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $locale
     */
    private $locale;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

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
     * @var Section
     */
    private $translatable;

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
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = Urlizer::urlize($slug);
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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
     * Set translatable
     *
     * @param Section $translatable
     */
    public function setTranslatable(Section $translatable)
    {
        $this->translatable = $translatable;
    }

    /**
     * Get translatable
     *
     * @return Section
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }
}
