<?php

namespace Unifik\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Unifik\SystemBundle\Lib\BaseEntity;
use Unifik\DoctrineBehaviorsBundle\Model as UnifikORMBehaviors;

/**
 * Represent an unifik application
 */
class App extends BaseEntity
{
    use UnifikORMBehaviors\Translatable\Translatable;
    use UnifikORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var int $order
     */
    private $order;

    /**
     * @var string $code
     */
    private $code;

    /**
     * @var integer $ordering
     */
    private $ordering;

    /**
     * @var array
     */
    private $sections;

    /**
     * @var Collection
     */
    private $navigations;

    /**
     * @var Collection
     */
    private $mappings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->navigations = new ArrayCollection();
        $this->mappings = new ArrayCollection();
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
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->getName()) {
            return $this->getName();
        } elseif ($this->getCode()) {
            return $this->getCode();
        }

        return 'New Application';
    }

    /**
     * Set order
     *
     * @param int $order The ordering number
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get the default route to entity
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getRouteBackend($suffix = 'edit')
    {
        $route = 'unifik_system_backend_application';

        if ($suffix) {
            $route .= '_' . $suffix;
        }

        return $route;
    }

    /**
     * Get Route Params
     *
     * @param array $params The params of the route
     *
     * @return array
     */
    public function getRouteBackendParams($params = array())
    {
        $defaults = array('applicationId' => intval($this->id));
        $params = array_merge($defaults, $params);

        return $params;
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
     * Add sections
     *
     * @param Section $sections The section to add
     */
    public function addSection(Section $sections)
    {
        $this->sections[] = $sections;
    }

    /**
     * Get sections
     *
     * @return Collection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Remove sections
     *
     * @param Section $sections
     */
    public function removeSection(Section $sections)
    {
        $this->sections->removeElement($sections);
    }

    /**
     * Add mappings
     *
     * @param Mapping $mappings
     *
     * @return App
     */
    public function addMapping(Mapping $mappings)
    {
        $this->mappings[] = $mappings;

        return $this;
    }

    /**
     * Remove mappings
     *
     * @param Mapping $mappings
     */
    public function removeMapping(Mapping $mappings)
    {
        $this->mappings->removeElement($mappings);
    }

    /**
     * Get mappings
     *
     * @return Collection
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * Add navigations
     *
     * @param Navigation $navigations
     *
     * @return App
     */
    public function addNavigation(Navigation $navigations)
    {
        $this->navigations[] = $navigations;

        return $this;
    }

    /**
     * Remove navigations
     *
     * @param Navigation $navigations
     */
    public function removeNavigation(Navigation $navigations)
    {
        $this->navigations->removeElement($navigations);
    }

    /**
     * Get navigations
     *
     * @return Collection
     */
    public function getNavigations()
    {
        return $this->navigations;
    }

    public function getCoreName()
    {
        return strtolower(str_replace(array('-', ' '), array('_', '_'), $this->getCode()));
    }

    /**
     * Check if it has children (for navigation)
     *
     * @return bool
     */
    public function hasChildren() {
        return ($this->getChildren()->count() > 0);
    }

    /**
     * Get Home Section (for navigation)
     *
     * @return Collection
     */
    public function getHomeSection() {
        $sections = $this->getSections();
        foreach ($sections as $section) {
            if ($section->isHomeSection()) {
                return $section;
            }
        }
        return false;
    }

    /**
     * Get children (for navigation)
     *
     * @return Collection
     */
    public function getChildren() {
        $sections = clone $this->getSections();
        $home = $this->getHomeSection();
        if ($home) {
            $sections->removeElement($home);
        }

        return $sections;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRouteFrontend()
    {
        $home = $this->getHomeSection();
        if ($home) {
            return 'section_id_' . $home->getId();
        }
        return false;
    }

    /**
     * Get Frontend route params
     *
     * @param array $params Array of params to get
     *
     * @return array
     */
    public function getRouteFrontendParams($params = array())
    {
        return $params;
    }
}
