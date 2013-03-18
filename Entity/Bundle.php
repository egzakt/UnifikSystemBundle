<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Bundle
 */
class Bundle extends BaseEntity
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var ArrayCollection
     */
    private $sections;

    /**
     * @var BundleParam
     */
    private $params;

    /**
     * @var SectionBundle
     */
    private $sectionBundles;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id the ID
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
     * Set name
     *
     * @param string $name The Name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string (EgzaktFrontendExampleBundle)
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the short name
     *
     * @return string (Example)
     */
    public function getShortName()
    {
        $tokens = $this->getTokenizedName();

        return $tokens[3];
    }

    /**
     * Get the app name
     *
     * @return string (Example)
     */
    public function getApp()
    {
        $tokens = $this->getTokenizedName();

        return $tokens[2];
    }

    /**
     * Set title
     *
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the string of the object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Add sections
     *
     * @param Section $sections Section to add
     */
    public function addSections(Section $sections)
    {
        $this->sections[] = $sections;
    }

    /**
     * Get sections
     *
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Add sectionBundles
     *
     * @param SectionBundle $sectionBundles SectionBundles to add
     */
    public function addSectionBundles(SectionBundle $sectionBundles)
    {
        $this->sectionBundles[] = $sectionBundles;
    }

    /**
     * Get sectionBundles
     *
     * @return ArrayCollection
     */
    public function getSectionBundles()
    {
        return $this->sectionBundles;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->name;
    }

    /**
     * Get route params
     *
     * @param array $params The params to get
     *
     * @return array
     */
    public function getRouteParams($params = array())
    {
        return $params;
    }

    /**
     * Add params
     *
     * @param BundleParam $params Params to add
     */
    public function addParams(BundleParam $params)
    {
        $this->params[] = $params;
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
     * This function try to fetch the parameter from the BundleParam table, if
     * it does not exist at that level it fall back to the container level.
     *
     * @param string $name The parameter name
     *
     * @return string|null
     */
    public function getParam($name)
    {
        // Looking at the BundleParam table level
        foreach ($this->getParams() as $param) {
            if ($param->getName() === $name) {
                return $param->getValue();
            }
        }

        // Looking at the bundle container level
        $nameTokens = $this->getTokenizedName();
        $underscoredBundleName = implode(array($nameTokens[1], $nameTokens[2], $nameTokens[3]), '_');
        $underscoredBundleName = strtolower($underscoredBundleName);

        if ($this->container->hasParameter($underscoredBundleName . '.' . $name)) {
            return $this->container->getParameter($underscoredBundleName . '.' . $name);
        }

        // Looking at the backend core container level
        if ($this->container->hasParameter('egzakt_backend_core' . '.' . $name)) {
            return $this->container->getParameter('egzakt_backend_core' . '.' . $name);
        }

        // The parameter does not exist
        return null;
    }

    /**
     * Add sectionBundles
     *
     * @param SectionBundle $sectionBundles SectionBundles to add
     */
    public function addSectionBundle(SectionBundle $sectionBundles)
    {
        $this->sectionBundles[] = $sectionBundles;
    }

    /**
     * Add params
     *
     * @param BundleParam $params Params to add
     */
    public function addBundleParam(BundleParam $params)
    {
        $this->params[] = $params;
    }

    /**
     * Get Tokenized Name
     *
     * @return array|null
     */
    public function getTokenizedName()
    {
        $tokens = array();

        preg_match('/(Egzakt|Project|Extend)([A-Z][a-z0-9]+)(.+)(Bundle)/', $this->getName(), $tokens);

        return $tokens;
    }
}