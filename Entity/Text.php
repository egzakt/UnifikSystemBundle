<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Text
 */
class Text extends BaseEntity
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var boolean $collapsable
     */
    protected $collapsable;

    /**
     * @var boolean $static
     */
    protected $static = false;

    /**
     * @var integer $ordering
     */
    protected $ordering;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * Set collapsable
     *
     * @param boolean $collapsable The collapsable state
     */
    public function setCollapsable($collapsable)
    {
        $this->collapsable = $collapsable;
    }

    /**
     * Get collapsable
     *
     * @return boolean
     */
    public function getCollapsable()
    {
        return $this->collapsable;
    }

    /**
     * Set static
     *
     * @param boolean $static Static state
     */
    public function setStatic($static)
    {
        $this->static = $static;
    }

    /**
     * Get static
     *
     * @return boolean
     */
    public function getStatic()
    {
        return $this->static;
    }

    /**
     * Is static
     *
     * @return boolean
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering The ordering number
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
     * Set createdAt
     *
     * @param \DateTime $createdAt Created At date/time
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt Updated At date/time
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
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
     * @param Section $section The section
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
     * Return a string representing the Text
     *
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New text';
        }

        if ($name = $this->translate()->getName()) {
            return $name;
        }

        if ($text = $this->translate()->getText()) {
            return $text;
        }

        // No translation found in the current locale
        return '';
    }

    /**
     * Get Route Backend
     *
     * @param string $action Action
     * @deprecated
     * @return string
     */
    public function getRoute($action = 'edit')
    {
        return $this->getRouteBackend($action);
    }

    /**
     * Get Route Backend
     *
     * @param string $action Action
     *
     * @return string
     */
    public function getRouteBackend($action = 'edit')
    {
        return 'EgzaktBackendTextBundle_' . $action;
    }

    /**
     * Get Route Backend Params
     *
     * @param array $params Route Params
     * @deprecated
     * @return array
     */
    public function getRouteParams($params = array())
    {
        return $this->getRouteBackendParams($params);
    }
    /**
     * Get Route Backend Params
     *
     * @param array $params Route Params
     *
     * @return array
     */
    public function getRouteBackendParams($params = array())
    {
        $defaults = array(
            'id' => $this->id ? $this->id : 0
        );
        $params = array_merge($defaults, $params);

        return $params;
    }

    /**
     * Add translations
     *
     * @param TextTranslation $translations
     */
    public function addTextTranslation(TextTranslation $translations)
    {
        $this->translations[] = $translations;
    }

    /**
     * Get translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * List of methods to check before allowing deletion
     *
     * @return array
     */
    public function getDeleteRestrictions()
    {
        return array('isStatic');
    }

    /**
     * Add translations
     *
     * @param  \Egzakt\SystemBundle\Entity\TextTranslation $translations
     * @return Text
     */
    public function addTranslation(\Egzakt\SystemBundle\Entity\TextTranslation $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Egzakt\SystemBundle\Entity\TextTranslation $translations
     */
    public function removeTranslation(\Egzakt\SystemBundle\Entity\TextTranslation $translations)
    {
        $this->translations->removeElement($translations);
    }
}
