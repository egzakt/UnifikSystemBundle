<?php

namespace Egzakt\SystemBundle\Lib;

use Symfony\Component\DependencyInjection\Container;

/**
 * Library of helper functions
 */
class Helper
{
    /**
     * @var Container
     */
    private $container;

    /**
     * Set container
     *
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Determine if an url is external
     *
     * @param string $url
     *
     * @return bool
     */
    public function isExternalUrl($url)
    {
        $parse = parse_url($url);

        if (isset($parse['host']) && !in_array($parse['host'], $this->container->getParameter('project_domains'))) {
            return true;
        }

        return false;
    }

    /**
     * Return the bundle name of an entity
     *
     * @param object $entity (example: Instance of Egzakt\Backend\SectionBundle\Entity\Section)
     *
     * @return string (example: EgzaktBackendSectionBundle)
     */
    public function getBundleNameFromEntity($entity)
    {
        $parts = explode('\\', get_class($entity));
        array_pop($parts); // remove the entity name
        array_pop($parts); // remove 'Entity'
        $bundleName = implode('', $parts);

        return $bundleName;
    }

    /**
     * Return the class name (without the namespace) of an entity
     *
     * @param object $entity (example: Instance of Egzakt\Backend\SectionBundle\Entity\Section)
     *
     * @return string (example: Section)
     */
    public function getClassNameFromEntity($entity)
    {
        $parts = explode('\\', get_class($entity));
        $className = array_pop($parts);

        return $className;
    }

    /**
     * Return the repository name of an entity
     *
     * @param object $entity (example: Instance of Egzakt\Backend\SectionBundle\Entity\Section)
     *
     * @return string (example: EgzaktBackendSectionBundle:Section)
     */
    public function getRepositoryNameFromEntity($entity)
    {
        return $this->getBundleNameFromEntity($entity) . ':' . $this->getClassNameFromEntity($entity);
    }

}