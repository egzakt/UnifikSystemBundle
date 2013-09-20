<?php

namespace Egzakt\SystemBundle\Extensions;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Library of helper functions
 */
class TranslationExtension extends \Twig_Extension
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var ArrayCollection
     */
    protected $locales;

    /**
     * @param Registry $doctrine
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * List of available filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'transTitle' => new \Twig_Filter_Method($this, 'transTitle')
        );
    }

    /**
     * This filter try to generate a string representation of an entity in the current locale.
     * If there is no usable reprentation available, a fallback machanism is launch and every
     * active locales are tried until one provides an usable result.
     *
     * @param BaseEntity $entity
     *
     * @return string
     */
    public function transTitle(BaseEntity $entity)
    {
        // Fallback not necessary, entity provides a usable string representation in the current locale.
        if ((string) $entity) {
            return $entity;
        }

        $entityPreviousLocale = $entity->getCurrentLocale();

        if (false == $this->locales) {
            $this->locales = $this->doctrine->getManager()->getRepository('EgzaktSystemBundle:Locale')->findBy(
                array('active' => true),
                array('ordering' => 'ASC')
            );
        }

        // fallback to other locales
        foreach ($this->locales as $locale) {

            if ($locale->getCode() === $entityPreviousLocale) {
                continue;
            }

            $entity->setCurrentLocale($locale->getCode());

            if ($fallback = (string) $entity) {
                $entity->setCurrentLocale($entityPreviousLocale);

                return $fallback;
            }
        }

        return '';
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'translation_extension';
    }
}
