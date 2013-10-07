<?php

namespace Flexy\SystemBundle\Entity;

use Flexy\DoctrineBehaviorsBundle\Model as FlexyORMBehaviors;

/**
 * Token
 */
class Token
{
    use FlexyORMBehaviors\Timestampable\Timestampable;

    public function __toString()
    {
        if (false == $this->id) {
            return 'New Token';
        }

        if ($token = $this->getToken()) {
            return $token;
        }

        // No token found
        return '';
    }

    /**
     * Get the backend route
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getRouteBackend($suffix = 'edit')
    {
        return 'flexy_system_backend_translation_' . $suffix;
    }

    /**
     * Get params for the backend route
     *
     * @param array $params Additional parameters
     *
     * @return array
     */
    public function getRouteBackendParams($params = array())
    {
        $defaults = array(
            'id' => $this->id ? $this->id : 0,
        );

        $params = array_merge($defaults, $params);

        return $params;
    }

    /**
     * Check if the translation for a locale exist, if it does it returns the TokenTranslation, if not it returns false
     *
     * @param $locale
     * @return bool||Translation
     */
    public function translationExist($locale)
    {
        foreach ($this->translations as $translation) {
            if ($translation->getLocale() == $locale) {
                return $translation;
            }
        }

        return false;
    }

    /**
     * Sort translations based on the locale's order
     *
     * @param $locales
     */
    public function sortTranslations($locales)
    {
        $newTranslations = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($locales as $locale) {
            foreach ($this->translations as $translation) {
                if ($translation->getLocale() == $locale->getCode()) {
                    $newTranslations[] = $translation;
                }
            }
        }

        $this->translations = $newTranslations;
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $token;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set token
     *
     * @param string $token
     * @return Token
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Add translations
     *
     * @param \Flexy\SystemBundle\Entity\TokenTranslation $translations
     * @return Token
     */
    public function addTranslation(\Flexy\SystemBundle\Entity\TokenTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Flexy\SystemBundle\Entity\TokenTranslation $translations
     */
    public function removeTranslation(\Flexy\SystemBundle\Entity\TokenTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}