<?php

namespace Unifik\SystemBundle\Lib\Frontend;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Unifik\SystemBundle\Entity\App;
use Unifik\SystemBundle\Entity\AppRepository;
use Unifik\SystemBundle\Entity\Section;
use Unifik\SystemBundle\Lib\ApplicationCoreInterface;
use Unifik\SystemBundle\Lib\Breadcrumbs;
use Unifik\SystemBundle\Lib\NavigationElementInterface;
use Unifik\SystemBundle\Lib\PageTitle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Core implements ApplicationCoreInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @var PageTitle
     */
    protected $pageTitle;

    /**
     * @var App The current app entity
     */
    protected $app;

    /**
     * @var Section The current section entity
     */
    protected $section;

    /**
     * @var NavigationElementInterface The current element (can be any entity implementing NavigationElementInterface)
     */
    protected $element;

    /**
     * @var array Array of elements
     */
    protected $elements;

    /**
     * @var bool
     */
    protected $initialized;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initialized = false;
    }

    /**
     * The name that represent the application
     *
     * @return string
     */
    public function getAppName()
    {
        return 'frontend';
    }

    /**
     * Init
     *
     * @throws NotFoundHttpException
     */
    public function init()
    {
        if (!$this->initialized) {
            $em = $this->doctrine->getManager();

            if ($sectionId = $this->getSectionId()) {
                $this->setSection(
                        $em->getRepository('UnifikSystemBundle:Section')->findOneBy(
                                array(
                                        'id' => $sectionId,
                                        'active' => true
                                )
                        )
                );
            }

            // If a section has been found
            if ($section = $this->getSection()) {
                $app = $section->getApp();
                if ($app->getId() != AppRepository::FRONTEND_APP_ID && $app->getId() != AppRepository::BACKEND_APP_ID && $app->getRoute()) {
                    $this->addNavigationElement($app);
                }

                foreach ($section->getParents() as $parent) {
                    $this->addNavigationElement($parent);
                }

                $this->addNavigationElement($section);
            } else {
                throw new NotFoundHttpException(
                        sprintf('The section_id_%s does not exist or is not active in the database', $sectionId)
                );
            }

            $this->initialized = true;
        }
    }

    /**
     * Add Navigation Element
     *
     * @param NavigationElementInterface $element The element to add
     */
    public function addNavigationElement(NavigationElementInterface $element)
    {
        $this->breadcrumbs->addElement($element);
        $this->pageTitle->addElement($element);

        $this->elements[] = $element;
        $this->element = $element;
    }

    /**
     * Remove a navigation element
     *
     * @param NavigationElementInterface $element
     */
    public function removeNavigationElement(NavigationElementInterface $element)
    {
        foreach ($this->elements as $k => $existingElement) {
            if ($element == $existingElement) {
                unset($this->elements[$k]);
                $this->breadcrumbs->removeElement($element);
                $this->pageTitle->removeElement($element);
                break;
            }
        }
    }

    /**
     * Get the current Section ID
     *
     * @return integer
     */
    public function getSectionId()
    {
        $unifikRequest = $this->request->attributes->get('_unifikRequest');

        return $unifikRequest['sectionId'] ?: 0;
    }

    /**
     * Get the current section entity
     *
     * @return Section
     */
    public function getSection()
    {
        if ($this->section) {
            return $this->section;
        }

        $em = $this->doctrine->getManager();

        $this->section = $em->getRepository('UnifikSystemBundle:Section')->find($this->getSectionId());

        return $this->section;
    }

    /**
     * Get appId
     *
     * @return integer
     */
    public function getAppId()
    {
        $unifikRequest = $this->request->attributes->get('_unifikRequest');

        return $unifikRequest['appId'] ?: 0;
    }

    /**
     * Get App
     *
     * @return App
     */
    public function getApp()
    {
        if ($this->app) {
            return $this->app;
        }

        $em = $this->doctrine->getManager();

        $this->app = $em->getRepository('UnifikSystemBundle:App')->find($this->getAppId());

        return $this->app;
    }

    /**
     * Get the current (last pushed) element entity.
     *
     * @return object
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Get Elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set Section
     *
     * @param Section $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }

    /**
     * Set Doctrine
     *
     * @param Registry $doctrine The Doctrine Registry
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Set Breadcrumbs
     *
     * @param Breadcrumbs $breadcrumbs The Breadcrumbs management object
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Set PageTitle
     *
     * @param PageTitle $pageTitle The PageTitle management object
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

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

}
