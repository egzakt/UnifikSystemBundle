<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseTranslationEntity;
use Egzakt\SystemBundle\Entity\Role;

/**
 * RoleTranslation
 */
class RoleTranslation extends BaseTranslationEntity
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $locale
     */
    protected $locale;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var Role
     */
    protected $translatable;

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
     * Set translatable
     *
     * @param Role $translatable
     */
    public function setTranslatable(Role $translatable)
    {
        $this->translatable = $translatable;
    }

    /**
     * Get translatable
     *
     * @return Role
     */
    public function getTranslatable()
    {
        return $this->translatable;
    }
}
