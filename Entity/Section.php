<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContext;

use Egzakt\SystemBundle\Lib\BaseEntity;
use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

/**
 * Section
 */
class Section extends BaseEntity
{
    use EgzaktORMBehaviors\Translatable\Translatable;
    use EgzaktORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var ArrayCollection
     */
    private $children;

    /**
     * @var Section
     */
    protected $parent;

    /**
     * @var string
     */
    private $app;

    /**
     * @var integer
     */
    private $ordering;

    /**
     * @var array
     */
    private $routeParams;

    /**
     * @var array
     */
    private $sectionNavigations;

    /**
     * @var array
     */
    private $texts;

    /**
     * @var ArrayCollection
     */
    private $roles;

    /**
     * @var ArrayCollection
     */
    private $mappings;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->mappings = new ArrayCollection();
        $this->sectionNavigations = new ArrayCollection();
    }

    public function __toString()
    {
        if (false == $this->id) {
            return 'New section';
        }

        if ($name = $this->getName()) {
            return $name;
        }

        // No translation found in the current locale
        return '';
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
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add children
     *
     * @param Section $children The Children to add
     */
    public function addChildren(Section $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        if ($this->hasChildren()) {

            // Temporary fix until we rewrite the navigation code
            if ($this->getSystemCore()->getCurrentAppName() != 'backend' && method_exists($this->children[0], 'getOrdering')) {

                $orderedChildren = array();
                foreach ($this->children as $child) {
                    $orderedChildren[$child->getOrdering()] = $child;
                }
                ksort($orderedChildren);

                return $orderedChildren;
            }

            return $this->children;
        }

        return array();
    }

    /**
     * Has children
     *
     * @return Boolean
     */
    public function hasChildren()
    {
        return (count($this->children));
    }

    /**
     * Set children
     *
     * @param array $children The children array to set
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Set parent
     *
     * @param Section $parent The Parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Section
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get parents
     *
     * @return array
     */
    public function getParents()
    {
        $parents = array();
        $tempParents = array();
        $parent = $this->getParent();
        $level = 1;

        while ($parent && $parent->getId()) {
            $tempParents[] = $parent;
            $parent = $parent->getParent();
        }

        $tempParents = array_reverse($tempParents);
        foreach ($tempParents as $parent) {
            $parents[$level] = $parent;
            $level++;
        }

        return $parents;
    }

    /**
     * Get parents slugs
     *
     * @return array
     */
    public function getParentsSlugs()
    {
        $slugs = array();

        /** @var $parent Section */
        foreach ($this->getParents() as $parent) {
            $slugs[] = $parent->getSlug();
        }

        return $slugs;
    }

    /**
     * Set app
     *
     * @param string $app The App
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * Get app
     *
     * @return string
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Add app
     *
     * @param App $app
     */
    public function addApp(App $app)
    {
        $this->app[] = $app;
    }

    /**
     * getLevel
     *
     * @return integer
     */
    public function getLevel()
    {
        return count($this->getParents()) + 1;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRouteFrontend()
    {
        return 'section_id_' . $this->id;
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

    /**
     * Get route
     *
     * @return string
     */
    public function getRouteBackend()
    {
        foreach ($this->mappings as $mapping) {
            if ($mapping->getType() == 'route' && $mapping->getApp()->getId() == AppRepository::BACKEND_APP_ID) {
                return $mapping->getTarget();
            }
        }
    }

    /**
     * Get Backend route params
     *
     * @return bool|array
     */
    public function getRouteBackendParams()
    {
        if ($this->routeParams) {
            return $this->routeParams;
        }

        return array(
            'sectionId' => $this->id
        );
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
     * Set Route
     *
     * @param string $route A route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Set Route Params
     *
     * @param array $params An array of params
     */
    public function setRouteParams($params)
    {
        $this->routeParams = $params;
    }

    /**
     * Get sectionNavigations
     *
     * @return ArrayCollection
     */
    public function getSectionNavigations()
    {
        return $this->sectionNavigations;
    }

    /**
     * Set sectionNavigations
     *
     * @param $sectionNavigations
     */
    public function setSectionNavigations($sectionNavigations)
    {
        $this->sectionNavigations = $sectionNavigations;
    }

    /**
     * Add children
     *
     * @param Section $children The Section to add as a child
     */
    public function addSection(Section $children)
    {
        $this->children[] = $children;
    }

    /**
     * Add sectionNavigations
     *
     * @param SectionNavigation $sectionNavigation SectionNavigation to add
     */
    public function addSectionNavigation(SectionNavigation $sectionNavigation)
    {
        $this->sectionNavigations[] = $sectionNavigation;
    }

    /**
     * Add text
     *
     * @param Text $text Text to add
     */
    public function addText(Text $text)
    {
        $this->texts[] = $text;
    }

    /**
     * Get texts
     *
     * @return ArrayCollection
     */
    public function getTexts()
    {
        return $this->texts;
    }

    /**
     * Set the array of texts
     *
     * @param array $texts An array of texts
     */
    public function setTexts($texts)
    {
        $this->texts = $texts;
    }

    /**
     * Returns the complete path of the section (Section / Sub-Section / Sub-sub-section ... )
     *
     * @return string
     */
    public function getHierarchicalName()
    {
        $return = $this->__toString();
        if ($this->getParent()) {
            $return = $this->parent->getHierarchicalName() . " / " . $return;
        }

        return $return;
    }

    /**
     * Checks if the section is in a specific navigation
     *
     * @param $navigationName
     *
     * @return bool
     */
    public function hasNavigation($navigationName)
    {
        foreach ($this->sectionNavigations as $sectionNavigation) {
            if ($sectionNavigation->getNavigation()->getName() == $navigationName) {
                return true;
            }
        }

        return false;
    }

    public function getHeadExtra()
    {
        return '';
    }

    /**
     * Basic verification to ensure the headCode contains html
     *
     * @param ExecutionContext $context
     *
     * @return bool
     */
    public function isHeadCodeHtml(ExecutionContext $context)
    {

        if ($this->getHeadCode() != '' && $this->getHeadCode() == strip_tags($this->getHeadCode())) {
            $propertyPath = $context->getPropertyPath() . '.translation.headCode';
            $context->setPropertyPath($propertyPath);
            $context->addViolation('You must put your content in html tags.', array(), null);

            return false;
        }

        return true;
    }

    /**
     * Get the navigations
     *
     * @return ArrayCollection
     */
    public function getNavigations()
    {
        $navigations = new ArrayCollection();

        foreach ($this->sectionNavigations as $sectionNavigation) {
            $navigations[] = $sectionNavigation->getNavigation();
        }

        return $navigations;
    }

    /**
     * Set the navigations
     *
     * @param $navigations ArrayCollection
     */
    public function setNavigations($navigations)
    {
        // Removing unassociated navigations
        foreach ($this->sectionNavigations as $key => $sectionNavigation) {
            if (false == $navigations->contains($sectionNavigation->getNavigation())) {
                unset($this->sectionNavigations[$key]);
            }
        }

        foreach ($navigations as $navigation) {

            // Already associated
            foreach ($this->sectionNavigations as $sectionNavigation) {
                if ($sectionNavigation->getNavigation() === $navigation) {
                    continue 2;
                }
            }

            // Has to be associated
            $sectionNavigation = new SectionNavigation();
            $sectionNavigation->setNavigation($navigation);
            $sectionNavigation->setSection($this);

            $this->sectionNavigations[] = $sectionNavigation;
        }
    }

    /**
     * Remove children
     *
     * @param \Egzakt\SystemBundle\Entity\Section $children
     */
    public function removeChildren(\Egzakt\SystemBundle\Entity\Section $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Add roles
     *
     * @param  \Egzakt\SystemBundle\Entity\Role $roles
     * @return Section
     */
    public function addRole(\Egzakt\SystemBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Set Roles
     *
     * @param ArrayCollection $roles
     */
    public function setRoles(ArrayCollection $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Remove roles
     *
     * @param \Egzakt\SystemBundle\Entity\Role $roles
     */
    public function removeRole(\Egzakt\SystemBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Remove texts
     *
     * @param \Egzakt\SystemBundle\Entity\Text $texts
     */
    public function removeText(\Egzakt\SystemBundle\Entity\Text $texts)
    {
        $this->texts->removeElement($texts);
    }

    /**
     * Remove sectionNavigations
     *
     * @param \Egzakt\SystemBundle\Entity\SectionNavigation $sectionNavigations
     */
    public function removeSectionNavigation(\Egzakt\SystemBundle\Entity\SectionNavigation $sectionNavigations)
    {
        $this->sectionNavigations->removeElement($sectionNavigations);
    }

    /**
     * Add mappings
     *
     * @param  \Egzakt\SystemBundle\Entity\Mapping $mappings
     * @return Section
     */
    public function addMapping(\Egzakt\SystemBundle\Entity\Mapping $mappings)
    {
        $this->mappings[] = $mappings;

        return $this;
    }

    /**
     * Remove mappings
     *
     * @param \Egzakt\SystemBundle\Entity\Mapping $mappings
     */
    public function removeMapping(\Egzakt\SystemBundle\Entity\Mapping $mappings)
    {
        $this->mappings->removeElement($mappings);
    }

    /**
     * Get mappings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMappings()
    {
        return $this->mappings;
    }

}
