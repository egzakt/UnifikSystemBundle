<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Locale
 */
class Locale extends BaseEntity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $switchName;

    /**
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $ordering;

    /**
     * @var boolean
     */
    private $active;

    /**
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New locale';
        }

        if ($this->name) {
            return $this->name;
        }

        // No translation found in the current locale
        return '';
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Locale
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
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
     * Set switchName
     *
     * @param string $switchName
     *
     * @return Locale
     */
    public function setSwitchName($switchName)
    {
        $this->switchName = $switchName;
    
        return $this;
    }

    /**
     * Get switchName
     *
     * @return string 
     */
    public function getSwitchName()
    {
        return $this->switchName;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return string
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

    /**
     * Set ordering
     *
     * @param integer $ordering
     *
     * @return Locale
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
     * Set active
     *
     * @param boolean $active
     * @return Locale
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
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
}