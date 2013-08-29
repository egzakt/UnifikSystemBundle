<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Represent an egzakt application
 */
class App extends BaseEntity
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $slug
     */
    protected $slug;

    /**
     * @var int $order
     */
    protected $order;

    /**
     * @var integer $ordering
     */
    protected $ordering;

    /**
     * @var string $prefix
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $sections;

    /**
     * @var Collection
     */
    protected $navigations;

    /**
     * @var Collection
     */
    protected $mappings;

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
        $route = 'egzakt_system_backend_application';

        if ($suffix) {
            $route .= '_' . $suffix;
        }

        return $route;
    }

    /**
     * Get slug
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
     * Set slug
     *
     * @param string $slug The slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * List of methods to check before allowing deletion
     *
     * @return array
     */
    public function getDeleteRestrictions()
    {
        return array('isRestrictedApp');
    }

    /**
     * Check if the current entity is a restricted app that is part of the system and should not be altered
     *
     * @return bool
     */
    public function isRestrictedApp()
    {
        $restrictedApps = array(AppRepository::FRONTEND_APP_ID, AppRepository::BACKEND_APP_ID);

        foreach ($restrictedApps as $restrictedApp) {
            if ($restrictedApp == $this->id) {
                return true;
            }
        }
    }
}