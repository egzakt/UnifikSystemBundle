<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use JMS\I18nRoutingBundle\Router\I18nRouter;

use Egzakt\SystemBundle\Lib\EntityInterface;
use Egzakt\SystemBundle\Entity\Section;

/**
 * Locale switcher
 */
class LocaleSwitcher
{
    /**
     * @var EntityInterface
     */
    protected $element;

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var I18nRouter
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Core
     */
    protected $core;

    /**
    * @var array
    */
    protected $parameters;

    /**
     * Construct
     */
    public function __construct(Container $container)
    {
        $this->setDoctrine($container->get('doctrine'));
        $this->router = $container->get('router');
        $this->core = $container->get('egzakt_frontend.core');
        $this->request = $container->get('request');

        $this->parameters = array();
    }

    /**
     * Generate an array of URL to switch to for each of the active locales
     *
     * @return array
     */
    public function generate()
    {
        $localizedUrls = array();

        $locales = $this->doctrine->getRepository('EgzaktSystemBundle:Locale')->findAllExcept($this->request->getLocale());

        // Get the object class (it may be a proxy...)
        $className = $this->getClassNameFromEntity($this->element);

        // Reload Element with all locales
        $element = $this->reloadElement($className);

        if ($element) {
            foreach ($locales as $locale) {

                $element->setLocale($locale->getCode());

                // If the homepage of the currently processed locale is not active, we jump to the next one.
                try {
                    $this->router->generate('section_id_1', array('_locale' => $locale->getCode()));
                } catch (\Exception $e) {
                    continue;
                }

                // Validating the element route parameters
                $parameters = $element->getRouteParams();
                $parameters['_locale'] = $locale->getCode();
                $parameters = array_filter($parameters); // Remove any FALSE values

                // Generating route ...
                try {
                    $url = $this->router->generate($element->getRoute(), $parameters);
                } catch (\Exception $e) {
                    // Fallback if no route found
                    $url = $this->fallBack($element, $locale);
                }

                $data = array();
                $data['locale'] = $locale;
                $data['url'] = $url;

                $localizedUrls[$locale->getCode()] = $data;
            }
        }

        // Clear the temporary Entity Manager
        $this->unsetEntityManager();

        return $localizedUrls;
    }

    /**
     * Fallback
     *
     * Fallback to the parent if it exists or to the current Section in the Core
     *
     * @param $element
     * @param $locale
     *
     * @return mixed
     */
    protected function fallBack($element, $locale)
    {
        if ($parent = $element->getParent()) {

            $this->setElement($parent);

            $data = $this->generate();
            $url = $data[$locale->getCode()]['url'];

        } elseif (false == $element instanceof Section) {

            $this->setElement($this->core->getSection());
            $data = $this->generate();
            $url = $data[$locale->getCode()]['url'];
        }

        return $url;
    }

    /**
     * Get Class Name From Entity
     *
     * Get the object class name (it may be a proxy...)
     *
     * @param $entity
     *
     * @return string
     */
    protected function getClassNameFromEntity($entity)
    {
        $className = get_class($entity);
        $reflectionClass = new \ReflectionClass($entity);

        if ($reflectionClass->implementsInterface('Doctrine\ORM\Proxy\Proxy')) {
            $className = $this->doctrine->getManager()->getClassMetadata($className)->name;
        }

        unset($reflectionClass);

        return $className;
    }

    /**
     * Reload Element
     *
     * Reload the element with all locales LEFT JOINED
     *
     * @param $className
     *
     * @return EntityInterface|object
     */
    protected function reloadElement($className)
    {
        // If a repository exists
        if (class_exists($className . 'Repository')) {
            $repository = $this->em->getRepository($className);
            $repository->setCurrentAppName('backend'); // Faking backend access to force a left join on future queries

            $element = $repository->find($this->element->getId());
        }
        // Otherwise, set the current element with this Custom Element
        else {
            $element = $this->element;
        }

        return $element;
    }

    /**
     * @param array $parameters The parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Set the element that will be used to generate the routes
     *
     * @param object $element The element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * Set doctrine
     *
     * @param Registry $doctrine The Doctrine Registry
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;

        // Create a temporary Entity Manager that we'll use to fetch objects on different locales
        $em = $this->doctrine->getManager();
        $this->em = $em::create($em->getConnection(), $em->getConfiguration());
    }

    /**
     * Unset Entity Manager
     *
     * Unset the temporary Entity Manager
     */
    protected function unsetEntityManager()
    {
        unset($this->em);
    }

}
