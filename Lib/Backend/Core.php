<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Egzakt\SystemBundle\Entity\App;
use Egzakt\SystemBundle\Entity\Bundle;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\SectionBundle;
use Egzakt\SystemBundle\Lib\Breadcrumbs;
use Egzakt\SystemBundle\Lib\NavigationInterface;
use Egzakt\SystemBundle\Lib\PageTitle;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Backend Core Class
 *
 * @throws \Exception
 */
class Core
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var String
     */
    private $locale;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * @var PageTitle
     */
    private $pageTitle;

    /**
     * The current app entity
     * @var App
     */
    private $app;

    /**
     * The current section entity
     * @var Section
     */
    private $section;

    /**
     * The current bundle entity
     * @var Bundle
     */
    private $bundle;

    /**
     * The current section bundle entity
     * @var SectionBundle
     */
    private $sectionBundle;

    /**
     * The current element (can be any entity implementing NavigationInterface)
     * @var object
     */
    private $element;

    /**
     * @var int $requestType
     */
    protected $requestType;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;

        if ($container->isScopeActive('request')) {
            $this->request = $container->get('request');
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Init
     */
    public function init()
    {
        if ($section = $this->getSection()) {

            foreach ($section->getParents() as $parent) {

                $this->addNavigationElement($parent);
            }
            $this->addNavigationElement($section);
        }
    }

    /**
     * Add an element to the Breadcrumbs and the Page Title
     *
     * @param NavigationInterface $element The element to push in the navigation stack
     *
     * @throws \Exception
     */
    public function addNavigationElement(NavigationInterface $element)
    {
        if (false == $element instanceof NavigationInterface) {
            throw new \Exception(
                'class ' . get_class($element) . ' must implements the NavigationInterface
                to be usable in the navigation, breadcrumbs and page title.'
            );
        }

        $this->breadcrumbs->addElement($element);
        $this->pageTitle->addElement($element);

        $this->element = $element;
    }

    /**
     * Get the current Section ID
     *
     * @return integer
     */
    public function getSectionId()
    {
        return $this->request->get('section_id', 0);
    }

    /**
     * Get the current Section
     *
     * @return Section
     */
    public function getSection()
    {
        if ($this->section) {
            return $this->section;
        }

        $em = $this->doctrine->getManager();

        $this->section = $em->getRepository('EgzaktSystemBundle:Section')->find($this->getSectionId());

        return $this->section;
    }

    /**
     * Get the current SectionBundle
     *
     * @return SectionBundle
     */
    public function getSectionBundle()
    {
        if ($this->sectionBundle) {
            return $this->sectionBundle;
        }
        $em = $this->doctrine->getEntityManager();

        $this->sectionBundle = $em->getRepository('EgzaktSystemBundle:SectionBundle')->findOneBy(array(
            'section' => $this->getSection() ? $this->getSection()->getId() : null,
            'bundle' => $this->getBundle() ? $this->getBundle()->getId() : null
        ));

        return $this->sectionBundle;
    }

    /**
     * Get the Bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        $controllerName = $this->request->get('_controller');
        $splitPosition = strpos($controllerName, 'Bundle') + 6;
        $bundleName = substr($controllerName, 0, $splitPosition);
        $bundleName = str_replace('\\', '', $bundleName);

        // If it's a ProjectBundle (deprecated) or a ExtendBundle that extends the EgzaktBundle, return the parent
        if (strpos($bundleName, 'Extend') !== false || strpos($bundleName, 'Project') !== false) {

            $parent = $this->kernel->getBundle($bundleName)->getParent();

            if (strpos($parent, 'Egzakt') !== false) {
                return $parent;
            }
        }

        return $bundleName;
    }

    /**
     * Get the Bundle
     *
     * @return Bundle The Bundle entity
     */
    public function getBundle()
    {
        if ($this->bundle) {
            return $this->bundle;
        }

        $em = $this->doctrine->getManager();

        $this->bundle = $em->getRepository('EgzaktSystemBundle:Bundle')->findOneBy(array(
            'name' => $this->getBundleName()
        ));

        return $this->bundle;
    }

    /**
     * Get the App
     *
     * @throws \Exception
     *
     * @return App The App entity
     */
    public function getApp()
    {
        if ($this->app) {
            return $this->app;
        }

        $appRepo = $this->doctrine->getManager()->getRepository('EgzaktSystemBundle:App');
        if ($this->request->get('app_slug')) {
            $this->app = $appRepo->findOneBy(array('slug' => $this->request->get('app_slug')));
            $method = 'route parameter {app_slug}';
        } elseif ($this->getSection()) {
            $this->app = $this->getSection()->getApp();
            $method = 'the section entity';
        } else {
            $this->app = $appRepo->findFirstOneExcept('backend');
            $method = 'first app entity in table (empty table?)';
        }

        if (false == $this->app) {
            throw new \Exception('the App entity could not be loaded. Method used: ' . $method);
        }

        return $this->app;
    }

    /**
     * Return the element
     *
     * @return object
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set the request object
     *
     * @param Request $request The Request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Get the request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the Doctrine Registry
     *
     * @param Registry $doctrine The Doctrine Registry
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Set the Symfony Kernel
     *
     * @param Kernel $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Set the Logger
     *
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the Breadcrumbs
     *
     * @param Breadcrumbs $breadcrumbs
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Set the Page Title
     *
     * @param PageTitle $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * Set the session object
     *
     * @param Session $session The Session
     *
     * @return void
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * Get the session
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set the locale
     *
     * @param string $locale The locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * getEditLocale
     *
     * Returns the edit locale, only the forms use this locale, the system locale is used everywhere else.
     *
     * @return string
     */
    public function getEditLocale()
    {
        if ($this->getRequest()->get('edit-locale')) {
            $this->getSession()->set('edit-locale', $this->getRequest()->get('edit-locale'));
        }

        if (!$this->getSession()->get('edit-locale')) {
            $this->getSession()->set('edit-locale', $this->getLocale());
        }

        return $this->getSession()->get('edit-locale');
    }

    /**
     * Set Request Type
     *
     * @param int $requestType
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
    }

    /**
     * Get Request Type
     *
     * @return int
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

}