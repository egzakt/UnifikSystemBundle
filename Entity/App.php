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
    use UnifikORMBehaviors\Sluggable\Sluggable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var int $order
     */
    private $order;

    /**
     * @var integer $ordering
     */
    private $ordering;

    /**
     * @var string $prefix
     */
    private $prefix;

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
        if ($this->id) {
            return $this->name;
        }

        return 'New Application';
    }

    /**
     * Set name
     *
     * @param string $name The name of the App
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
     * Get the default route to entity
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getRoute($suffix = 'edit')
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
    public function getRouteParams($params = array())
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
     * Set prefix
     *
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
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

    /**
     * Get Sluggable Fields
     *
     * @return array
     */
    public function getSluggableFields()
    {
        return array('name');
    }

    public function getCoreName()
    {
        return strtolower(str_replace(array('-', ' '), array('_', '_'), $this->getSlug()));
    }
}
