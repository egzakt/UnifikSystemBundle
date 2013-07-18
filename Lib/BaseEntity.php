<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Egzakt\SystemBundle\Lib\EntityInterface;
use Egzakt\SystemBundle\Lib\NavigationElementInterface;

/**
 * Egzakt Backend Base for Entities
 */
abstract class BaseEntity implements EntityInterface, NavigationElementInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * The element is currently selected.
     * Used in navigations.
     *
     * @var boolean
     */
    private $selected;

    /**
     * The level of the element in the navigation
     *
     * @var integer
     */
    private $level;

    /**
     * The parent element
     *
     * @var BaseEntity
     */
    private $parent;

    /**
     * The container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The route name
     *
     * @var string
     */
    protected $route;

    /**
     * The locale
     *
     * @var string
     */
    private $locale;

    /**
     * Locales in which the entity is available
     *
     * @var array
     */
    protected $locales;

    /**
     * Returns the entity name without its path
     *
     * @return string
     */
    public function getEntityName()
    {
        $className = get_class($this);
        $classNameTokens = explode('\\', $className);

        return array_pop($classNameTokens);
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the children
     *
     * @return array
     */
    public function getChildren()
    {
        return array();
    }

    /**
     * Return true if the entity has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return false;
    }

    /**
     * Gets the Parent
     *
     * @return bool
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the Parent
     *
     * @param object $parent The parent object
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns true if selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * Sets the selected state
     *
     * @param boolean $bool The selected state
     */
    public function setSelected($bool)
    {
        $this->selected = $bool;
    }

    /**
     * Returns true if active
     *
     * @return bool
     */
    public function isActive()
    {
        return false;
    }

    /**
     * Returns true if editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * Returns true if deletable
     *
     * @return bool
     */
    public function isDeletable()
    {
        if (!$this->getId()) {
            return false;
        }

        if (method_exists($this, 'getDeleteRestrictions')) {
            foreach ($this->getDeleteRestrictions() as $method) {

                $result = $this->$method();

                if ((is_bool($result) && $result == true) || (!is_bool($result) && count($result))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get Level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set Level
     *
     * @param integer $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Sets the Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Gets the System Core
     *
     * @return \Egzakt\System\CoreBundle\Lib\Core
     */
    protected function getSystemCore()
    {
        return $this->container->get('egzakt_system.core');
    }

    /**
     * Gets the current application core
     *
     * @return \Egzakt\Backend\CoreBundle\Lib\Core
     */
    public function getCore()
    {
        return $this->container->get('egzakt_' . $this->getSystemCore()->getCurrentAppName() . '.core');
    }

    /**
     * Gets the Route of the entity
     *
     * @param string $suffix The suffix to be concatenated after the Route
     *
     * @return string
     */
    public function getRoute($suffix = '')
    {
        if ($this->route) {
            return $this->route;
        }

        $currentAppName = ucfirst($this->getSystemCore()->getCurrentAppName());
        $methodName = 'getRoute' . $currentAppName;

        // Fallback to frontend method if the current app does not define any method
        if (false == method_exists($this, $methodName) && 'Backend' !== $currentAppName) {
            $methodName = 'getRouteFrontend';
        }

        if ($suffix) {
            $route = $this->$methodName($suffix);
        } else {
            $route = $this->$methodName();
        }

        return $route;
    }

    /**
     * Get the Route Params
     *
     * @param array $params Params to get
     *
     * @return array
     */
    public function getRouteParams($params = array())
    {
        $currentAppName = ucfirst($this->getSystemCore()->getCurrentAppName());
        $methodName = 'getRoute' . $currentAppName . 'Params';

        // Fallback to frontend method if the current app does not define any method
        if (false == method_exists($this, $methodName) && 'Backend' !== $currentAppName) {
            $methodName = 'getRouteFrontendParams';
        }

        $params = $this->$methodName($params);

        return $params;
    }

    /**
     * Especially created to take I18N fields for set/get fields too
     *
     * @param string $method    The name of the method
     * @param array  $arguments Arguments sent to this method
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $matches = array();
        if (preg_match('/^(set|get)(.*)$/', $method, $matches)) {
            $property = strtolower($matches[2]);
            if (!property_exists($this, $property)) {
                switch ($matches[1]) {
                    case 'get':
                        if (method_exists($this->translate(), $method)) {
                            return call_user_func_array(array($this->translate(), $method), $arguments);
                        }
                    case 'set':
                        if (method_exists($this->translate(), $method)) {
                            return call_user_func_array(array($this->translate(), $method), $arguments);
                        }
                }
            }
        }

        throw new \Exception('Call to undefined method : ' . $method);
    }

    /**
     * Magic __get function
     *
     * This methods allows to get translatable fields from parent entity
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (!property_exists($this, $property)) {
            if (method_exists($this->translate(), $getter = 'get'.ucfirst($property))) {
                return $this->translate()->$getter();
            }
        }

        throw new \Exception('Call to undefined property : ' . $property);
    }

    /**
     * Magic __set function
     *
     * This methods allows to set translatable fields from parent entity
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __set($property, $value)
    {
        if (!property_exists($this, $property)) {
            if (method_exists($this->translate(), $setter = 'set'.ucfirst($property))) {
                return $this->translate()->$setter($value);
            }
        }

        throw new \Exception('Trying to set an undefined property : ' . $property);
    }

    /**
     * Magic __isset function
     *
     * This method is being called to check if a property exists, in a translation entity
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        // If the property doesn't exist in this class...
        if (!property_exists($this, $name)) {
            // We take a look at the translation class
            if (property_exists($this->translate(), $name)) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * setLocale
     *
     * Sets the current locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * getLocale
     *
     * Gets the current locale
     *
     * @return string
     */
    public function getLocale()
    {
        if (!$this->locale) {
            // In the Backend application, we want the editLocale
            if ($this->getSystemCore()->getCurrentAppName() == 'backend') {
                $this->locale = $this->getCore()->getEditLocale();
                return $this->locale;
            }

            if ($locale = $this->container->get('request')->getLocale()) {
                $this->locale = $locale;
                return $this->locale;
            }

            // System locale
            $this->locale = $this->container->getParameter('locale');
        }

        return $this->locale;
    }

    /**
     * Locales in which the entity is available
     *
     * @return array
     */
    public function getLocales()
    {

        if ($this->locales) {
            return $this->locales;
        }

        $this->locales = array();

        if (method_exists($this, 'getTranslations')) {
            foreach ($this->getTranslations() as $trans) {
                $this->locales[] = $trans->getLocale();
            }
        } else {
            $this->locales[] = $this->getLocale();
        }

        return $this->locales;
    }

    /**
     * translate
     *
     * @param string $locale
     *
     * @return \Egzakt\Backend\CoreBundle\Lib\BaseTranslationEntity
     */
    public function translate($locale = null)
    {
        if (property_exists($this, 'translations')) {
            if (null === $locale) {
                $locale = $this->getLocale();
            }

            foreach ($this->translations as $translation) {
                if ($translation->getLocale() === $locale) {
                    return $translation;
                }
            }

            $translationClass = get_class($this) . 'Translation';

            // Support Doctrine Proxies...
            $reflectionClass = new \ReflectionClass($this);
            if ($reflectionClass->implementsInterface('Doctrine\ORM\Proxy\Proxy')) {
                $parentClass = $this->container->get('doctrine')->getManager()->getClassMetadata(get_class($this))->name;
                $translationClass = $parentClass . 'Translation';
            }
            unset($reflectionClass);

            $translation = new $translationClass();
            $translation->setLocale($locale);
            $translation->setTranslatable($this);
            $this->translations[] = $translation;

            return $translation;
        }

        return $this;
    }

    /**
     * getTranslation
     *
     * @param string $locale The locale in which we want to get the translation entity
     *
     * @return \Egzakt\Backend\CoreBundle\Lib\BaseTranslationEntity
     */
    public function getTranslation($locale = null)
    {
        if (property_exists($this, 'translations')) {
            if (null === $locale) {
                $locale = $this->getLocale();
            }

            return $this->translate($locale);
        }

        return null;
    }

}