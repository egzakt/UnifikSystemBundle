<?php

namespace Egzakt\SystemBundle\Lib\Frontend;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Egzakt\SystemBundle\Entity\App;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Lib\ApplicationCoreInterface;
use Egzakt\SystemBundle\Lib\Breadcrumbs;
use Egzakt\SystemBundle\Lib\NavigationElementInterface;
use Egzakt\SystemBundle\Lib\PageTitle;
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
        $em = $this->doctrine->getManager();

        if ($sectionId = $this->getSectionId()) {
            $this->setSection($em->getRepository('EgzaktSystemBundle:Section')->findOneBy(array(
                'id' => $sectionId,
                'active' => true
            )));
        }

        // If a section has been found
        if ($section = $this->getSection()) {
            foreach ($section->getParents() as $parent) {
                $this->addNavigationElement($parent);
            }

            $this->addNavigationElement($section);
        } else {
            throw new NotFoundHttpException(sprintf('The section_id_%s does not exist or is not active in the database', $sectionId));
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
        $egzaktRequest = $this->request->attributes->get('_egzaktRequest');

        return $egzaktRequest['sectionId'] ?: 0;
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

        $this->section = $em->getRepository('EgzaktSystemBundle:Section')->find($this->getSectionId());

        return $this->section;
    }

    /**
     * Get appId
     *
     * @return integer
     */
    public function getAppId()
    {
        $egzaktRequest = $this->request->attributes->get('_egzaktRequest');

        return $egzaktRequest['appId'] ?: 0;
    }

    /**
     * Should be in the backend only
     *
     * @return App
     */
    public function getApp()
    {
        if ($this->app) {
            return $this->app;
        }

        $em = $this->doctrine->getManager();

        $this->app = $em->getRepository('EgazktSystemBundle:App')->find($this->getAppId());

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