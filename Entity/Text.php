<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Egzakt\SystemBundle\Lib\BaseEntity;
use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

/**
 * Text
 */
class Text extends BaseEntity
{
    use EgzaktORMBehaviors\Translatable\Translatable;
    use EgzaktORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var boolean $collapsable
     */
    private $collapsable;

    /**
     * @var boolean $static
     */
    private $static = false;

    /**
     * @var integer $ordering
     */
    private $ordering;

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
        return 'egzakt_system_backend_text_' . $action;
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
     * List of methods to check before allowing deletion
     *
     * @return array
     */
    public function getDeleteRestrictions()
    {
        return array('isStatic');
    }

}