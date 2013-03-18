<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Section Bundle
 */
class SectionBundle extends BaseEntity
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Section
     */
    private $section;

    /**
     * @var string $bundle
     */
    private $bundle;

    /**
     * @var SectionBundleParam
     */
    private $params;

    /**
     * @var integer $ordering
     */
    private $ordering;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->params = new ArrayCollection();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getBundle()->getTitle();
    }

    /**
     * Set id
     *
     * @param integer $id The SectionBundle ID
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set section
     *
     * @param Section $section The Section
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
     * Set bundle
     *
     * @param string $bundle The Bundle
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * Get bundle
     *
     * @return Bundle
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering
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
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->getBundle()->getRoute();
    }

    /**
     * Get route params
     *
     * @param array $params An array of params
     *
     * @return array
     */
    public function getRouteParams($params = array())
    {
        $params['section_id'] = $this->getSection()->getId();

        return $this->getBundle()->getRouteParams($params);
    }

    /**
     * Add params
     *
     * @param SectionBundleParam $param A Param to add
     */
    public function addParams(SectionBundleParam $param)
    {
        $this->params[] = $param;
    }

    /**
     * Get params
     *
     * @return ArrayCollection
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a specific parameter
     *
     * The parameter name is passed without the bundle name
     * Ex: 'max_level' will translate to 'this_bundle_name.max_level' value
     *
     * This function try to fetch the parameter from the SectionBundleParam table, if
     * it does not exist at that level it fall back to the BundleParam table level.
     *
     * @param string $name The parameter name
     *
     * @return string|null
     */
    public function getParam($name)
    {
        foreach ($this->getParams() as $param) {
            if ($param->getName() == $name) {
                return $param->getValue();
            }
        }

        return $this->getBundle()->getParam($name);
    }

    /**
     * Get params merged from the bundle application wide params
     * When defined, Params from the SectionBundle override the bundle application wide params
     *
     * @return ArrayCollection
     */
    public function getMergedParams()
    {
        $mergedParams = new ArrayCollection();

        $bundleParams = $this->getBundle()->getParams();
        $sectionBundleParams = $this->getParams();

        foreach ($sectionBundleParams as $sectionBundleParam) {
            $mergedParams->add($sectionBundleParam);
        }

        foreach ($bundleParams as $bundleParam) {
            $mergedParams->add($bundleParam);
        }

        return $mergedParams;
    }

    /**
     * Get a specific merged param via his name
     *
     * @param string $paramName The param name
     *
     * @return SectionBundleParam
     */
    public function getMergedParam($paramName)
    {
        foreach ($this->getMergedParams() as $param) {

            if ($param->getName() == $paramName) {
                return $param;
            }
        }

        return null;
    }

    /**
     * Add params
     *
     * @param SectionBundleParam $params
     */
    public function addSectionBundleParam(SectionBundleParam $params)
    {
        $this->params[] = $params;
    }
}