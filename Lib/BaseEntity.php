<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Egzakt\SystemBundle\Lib\EntityInterface;
use Egzakt\SystemBundle\Lib\NavigationInterface;

/**
 * Egzakt Backend Base for Entities
 */
abstract class BaseEntity implements EntityInterface, NavigationInterface
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
     * Locales in which the entity is available
     *
     * @var string
     */
    protected $i18nTitle;


    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->getId()) {
            // Return i18n toString method if it exists
            if (property_exists($this, 'translations')) {
                $name = $this->translate()->__toString();

                if ($name || ($this->getSystemCore()->getCurrentAppName() != 'backend')) {
                    return $name;

                } elseif ($this->container->get('request')->getLocale() != $this->getLocale()) {
                    $name = $this->translate($this->container->get('request')->getLocale())->__toString();

                    if (!$name) {
                        return 'No traduction';
                    }

                    return '<span class=\'translated\'>' . $name . '</span>';
                }
            }

            if (method_exists($this, 'getName')) {
                return $this->getName() ? $this->getName() : 'Untitled';
            }

            // DEPRECATED
            if (method_exists($this, 'getNom')) {
                return $this->getNom() ? $this->getNom() : 'Untitled';
            }

            return $this->getEntityName();
        }

        return 'New ' . $this->getEntityName();
    }

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
     * Get Absolute Path
     *
     * @param string $field  Name of the field containing the filename
     * @param string $prefix Prefix to the filename
     * @param string $locale
     *
     * @return null|string
     */
    public function getAbsolutePath($field, $prefix = null, $locale = null)
    {
        if ($locale) {
            $translation = $this->getTranslation($locale);

            if ($translation) {
                $filename = $this->getTranslation($locale)->{'get' . ucfirst($field)}();
            }
        }

        if (!isset($filename)) {
            $filename = $this->{'get' . ucfirst($field)}();
        }

        if (null == $filename) {
            return null;
        }

        if ($prefix) {
            $prefix .= '_';
        }

        return $this->getUploadRootDir() . '/' . $prefix . $filename;
    }

    /**
     * Get Web Path
     *
     * @param string $field  Name of the field containing the filename
     * @param string $prefix Prefix to the filename
     *
     * @return null|string
     */
    public function getWebPath($field, $prefix = null, $absolutePath = false)
    {
        if (null == $this->{'get' . ucfirst($field)}()) {
            return null;
        }

        $uploadDir = $this->getUploadDir();
        if (!$this->container || $this->container->get('kernel')->getEnvironment() == 'test') {
            $uploadDir = 'web/' . $uploadDir;
        }

        if ($prefix) {
            $prefix .= '_';
        }

        if($absolutePath){
            return "http://" . $_SERVER["SERVER_NAME"] . '/' . $uploadDir . '/' . $prefix . $this->{'get' . ucfirst($field)}();
        }

        return '/' . $uploadDir . '/' . $prefix . $this->{'get' . ucfirst($field)}();
    }

    /**
     * Check if the file exists
     *
     * @param string $field  Name of the field containing the filename
     * @param string $prefix Prefix to the filename
     *
     * @return bool
     */
    public function fileExists($field, $prefix = null)
    {
        return is_file($this->getAbsolutePath($field, $prefix));
    }

    /**
     * Resizes the image and returns its web path
     *
     * @param string $field      Name of the field containing the filename
     * @param string $formatName Name of the format
     *
     * @return null|string
     */
    public function getResizedImage($field, $formatName)
    {
        // Returns the image manually cropped by the client
        if ($this->fileExists($field, $formatName)) {
            return $this->getWebPath($field, $formatName);
        }

        // Returns the image after resizing it using GregwarImageBundle
        // If the image has already been resized (with these exact specs) before,
        // it will use the GregwarImageBundle cache system
        $formats = $this->getImageFormats($field);
        $format = $formats[$formatName];

        $image = $this->container->get('image.handling')->open($this->getAbsolutePath($field));
        $type = $image->guessType();

        if (isset($format['width']) && isset($format['height'])) {
            return $image->centeredCrop($format['width'], $format['height'])->{$type}();

        } elseif (isset($format['width'])) {
            return $image->cropResize($format['width'], null, 'transparent')->{$type}();

        } elseif (isset($format['height'])) {
            return $image->cropResize(null, $format['height'], 'transparent')->{$type}();

        } elseif (isset($format['max'])) {
            return $image->max($format['max'])->{$type}();
        }
    }

    /**
     * The absolute directory path where uploaded files should be saved
     *
     * @return string
     */
    protected function getUploadRootDir()
    {
        return $this->container->getParameter('kernel.root_dir') . '/../web/' . $this->getUploadDir();
    }

    /**
     * Upload files
     *
     * @param BaseEntity $oldEntity Needed to delete the old image
     */
    public function upload($oldEntity = null)
    {
        foreach ($this->fileFields as $field) {

            // (Each field must have a corresponding \Symfony\Component\HttpFoundation\File\UploadedFile property)
            if ($this->{$field . 'File'} !== null) {

                // Delete the old file
                if ($oldEntity) {
                    $oldEntity->removeUpload(array($field));
                }

                // sanitize the filename and prefix it with the entity's id to make it unique
                $fileExtension = $this->{$field . 'File'}->guessExtension();
                $fileName = preg_replace('/' . $fileExtension . '$/', '', $this->{$field . 'File'}->getClientOriginalName());
                $sanitizedFilename = $this->getId() . '-' . Urlizer::urlize($fileName) . '.' . $fileExtension;

                // move the uploaded file to the right directory and rename it with the new sanitized name
                $this->{$field . 'File'}->move($this->getUploadRootDir(), $sanitizedFilename);

                $this->{'set' . ucfirst($field)}($sanitizedFilename);
                $this->{$field . 'File'} = null;
            }
        }
    }

    /**
     * Delete uploaded files
     *
     * @param array   $fields             List of fields that use the upload functionnality and store the filename
     * @param boolean $deleteTranslations Determine if the files must be deleted for all the translations
     */
    public function removeUpload($fields = null, $deleteTranslations = false)
    {
        // If no specific fields were passed, we get them all
        if (!$fields) {
            $fields = $this->fileFields;
            $deleteTranslations = true;
        }

        foreach ($fields as $field) {
            if ($file = $this->getAbsolutePath($field)) {

                $i18nField = false;

                // Check if the field is i18n
                if (property_exists($this, 'translations')) {
                    if (property_exists($this->getTranslation(), $field)) {

                        $i18nField = true;

                        foreach ($this->getLocales() as $locale) {

                            // Delete the original file in the current locale only, unless otherwise specified
                            if ($deleteTranslations || (!$deleteTranslations && $locale == $this->getLocale())) {

                                $file = $this->getAbsolutePath($field, null, $locale);

                                if (is_file($file)) {
                                    unlink($file);
                                }
                            }
                        }
                    }
                }

                if (!$i18nField && is_file($file)) {
                    unlink($file);
                }

                // Delete all formats of this file (applies only to images)
                if (method_exists($this, 'getImageFormats')) {
                    if ($formats = $this->getImageFormats($field)) {

                        // Check if the field is i18n
                        if ($i18nField) {

                            foreach ($this->getLocales() as $locale) {

                                // Delete the resized file in the current locale only, unless otherwise specified
                                if ($deleteTranslations || (!$deleteTranslations && $locale == $this->getLocale())) {

                                    foreach ($formats as $formatName => $formatParams) {
                                        $resizedFile = $this->getAbsolutePath($field, $formatName, $locale);

                                        if (is_file($resizedFile)) {
                                            unlink($resizedFile);
                                        }
                                    }
                                }
                            }

                        } else {
                            foreach ($formats as $formatName => $formatParams) {
                                $resizedFile = $this->getAbsolutePath($field, $formatName);

                                if (is_file($resizedFile)) {
                                    unlink($resizedFile);
                                }
                            }
                        }
                    }
                }

                // Clear the field
                $this->{'set' . ucfirst($field)}(null);
            }
        }
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

    /**
     * Returns the i18n name of an object
     * and displays the i18n name of the original locale in brackets as a reference
     *
     * @return string
     */
    public function getI18nTitle()
    {
        if (!$this->i18nTitle) {

            // New item
            if (!$this->getId()) {
                return $this->i18nTitle = $this->__toString();
            }

            $name = '';
            $referenceName = '';

            foreach ($this->translations as $translation) {

                if ($translation->getLocale() === $this->getLocale()) {
                    $name = $translation->__toString();

                } elseif ($this->container->get('request')->getLocale() == $translation->getLocale()) {
                    $referenceName = $translation->__toString();

                } elseif (!$referenceName) {
                    $referenceName = $translation->__toString();
                }
            }

            $referenceName = strip_tags($referenceName);

            if ($name) {

                $name = strip_tags($name);

                if ($this->container->get('request')->getLocale() == $this->getLocale() || !$referenceName) {
                    $this->i18nTitle = $name;
                } else {
                    $this->i18nTitle = $name . ' <em>(' . $referenceName . ')</em>';
                }

            } else {
                $this->i18nTitle = '<em>(' . $referenceName . ')</em>';
            }
        }

        return $this->i18nTitle;
    }

}