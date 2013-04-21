<?php

namespace Egzakt\SystemBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\SectionBundle;
use Egzakt\SystemBundle\Entity\SectionRepository;
use Egzakt\SystemBundle\Entity\GlobalModule;

/**
 * Navigation Controller
 */
class NavigationController extends BaseController
{
    /**
     * @var SectionRepository
     */
    protected $sectionRepository;

    /**
     * Init
     */
    public function init()
    {
	$this->sectionRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Section');
    }

    /**
     * Section Bar Action
     *
     * @return Response
     */
    public function sectionBarAction()
    {
        $sectionCurrent = $this->getSection();
        $appCurrent = $this->getApp();

        if (false == $sectionCurrent) {
            $sectionCurrent = new Section();
        }

        $appId = 2; // debug hardcoded frontend

	$sections = $this->sectionRepository->findAll();

        $navigationBuilder = $this->get('egzakt_system.navigation_builder');
        $navigationBuilder->setElements($sections);
        $navigationBuilder->setSelectedElement($sectionCurrent);
        $navigationBuilder->build();

        $sections = $navigationBuilder->getElements();

        return $this->render('EgzaktSystemBundle:Backend/Navigation:section_bar.html.twig', array(
            'sections' => $sections,
            'sectionCurrent' => $sectionCurrent
        ));
    }

    /**
     * Global Bundle Bar Action
     *
     * @param $masterRoute
     *
     * @return Response
     */
    public function globalBundleBarAction($masterRoute)
    {
        $mappings = $this->getEm()->getRepository('EgzaktSystemBundle:Mapping')->findBy(array('navigation' => 3), array('ordering' => 'ASC'));

        return $this->render('EgzaktSystemBundle:Backend/Navigation:global_bundle_bar.html.twig', array(
            'mappings' => $mappings,
            'masterRoute' => $masterRoute
        ));
    }

    /**
     * Header Action
     *
     * @return Response
     */
    public function headerAction()
    {
        return $this->render('EgzaktSystemBundle:Backend/Navigation:header.html.twig');
    }

    /**
     * Application selection navigation
     *
     * @return Response
     */
    public function appAction()
    {
        $appCurrent = $this->getCore()->getApp();

        $appRepo = $this->getDoctrine()->getRepository('EgzaktBackendCoreBundle:App');
        $apps = $appRepo->findBy(array(), array('ordering' => 'asc'));

        // BC fix, previous version had a "backend" application that need to be removed
        foreach ($apps as $key => $app) {
            if ($app->getName() == 'backend') {
                unset($apps[$key]);
            }
        }

        return $this->render('EgzaktBackendCoreBundle:Navigation:app.html.twig', array(
            'apps' => $apps,
            'appCurrent' => $appCurrent
        ));
    }

    /**
     * Breadcrumbs Action
     *
     * @return Response
     */
    public function breadcrumbsAction()
    {
        $elementCurrent = $this->getCore()->getElement();
        $elements = $this->get('egzakt_backend.breadcrumbs')->getElements();
        $sectionCurrent = $this->getCore()->getSection();

        if ($this->getSection()) {
            $truncateLength = $this->getSectionBundle()->getParam('breadcrumbs_truncate_length');
        } else {
            // Exception for the login and dashboard pages
            $truncateLength = $this->container->getParameter('egzakt_backend_core.breadcrumbs_truncate_length');
        }

        return $this->render('EgzaktBackendCoreBundle:Navigation:breadcrumbs.html.twig', array(
            'elements' => $elements,
            'elementCurrent' => $elementCurrent,
            'section' => $sectionCurrent,
            'truncateLength' => $truncateLength
        ));
    }

    /**
     * Page Title Action
     *
     * @return Response
     */
    public function pageTitleAction()
    {
        $elementCurrent = $this->getCore()->getElement();
        $elements = $this->get('egzakt_backend.page_title')->getElements();

        if ($this->getSectionBundle()) {
            $truncateLength = $this->getSectionBundle()->getParam('breadcrumbs_truncate_length');
        } else {
            // Exception for the login and dashboard pages
            $truncateLength = $this->container->getParameter('egzakt_backend_core.breadcrumbs_truncate_length');
        }

        return $this->render('EgzaktBackendCoreBundle:Navigation:page_title.html.twig', array(
            'elements' => $elements,
            'elementCurrent' => $elementCurrent,
            'truncateLength' => $truncateLength
        ));
    }

    /**
     * Bundle Bar Action
     *
     * @param $masterRoute
     * @return Response
     */
    public function bundleBarAction($masterRoute)
    {
        if (false == $this->getSection()) {
            return new Response();
        }

        $section = $this->getSection();
        $mappings = $this->getEm()->getRepository('EgzaktSystemBundle:Mapping')->findBy(array(
            'section' => $section,
            'navigation' => 3
        ));

        return $this->render('EgzaktSystemBundle:Backend/Navigation:bundle_bar.html.twig', array(
            'mappings' => $mappings,
            'masterRoute' => $masterRoute,
            'section' => $section
        ));
    }

    /**
     * List of tabs available when editing an entity
     *
     * @param Entity $entity
     * @param array  $tabs List of tabs
     *
     * @return Response
     * @throws Exception
     */
    public function tabsAction($entity, $tabs = null)
    {
        if (!$tabs) {
            $tabs = $this->getSectionBundle()->getParam('tabs');
        }

        $tabCurrent = $this->getCore()->getRequest()->get('tab');

        if (!$tabCurrent) {
            throw new Exception('Selected tab name not found. Maybe you forgot to add a "tab" route default value? Available values: ' . implode(', ', array_keys($tabs)));
        }

        // Determine which tab is selected
        $tabs[$tabCurrent]['selected'] = true;

        return $this->render('EgzaktBackendCoreBundle:Navigation:tabs.html.twig', array(
            'tabs' => $tabs,
            'entity' => $entity
        ));
    }

    /**
     * Remove Inactive Bundles From Sections
     *
     * @param array $sections Array of Sections object
     *
     * @return array
     */
    private function removeInactiveBundlesFromSections($sections)
    {
        $bundles = $this->get('kernel')->getBundles();

        /** @var Section $section */
        foreach ($sections as $section) {

            $sectionBundles = array();

            /** @var SectionBundle $sectionBundle */
            foreach ($section->getSectionBundles() as $sectionBundle) {
                if (in_array($sectionBundle->getBundle()->getName(), array_keys($bundles))) {
                    $sectionBundles[] = $sectionBundle;
                }
            }

            $section->setSectionBundles($sectionBundles);
        }

        return $sections;
    }

    /**
     * Locale Bar Action
     *
     * @return Response
     */
    public function localeBarAction()
    {
        $localeRepo = $this->getEm()->getRepository('EgzaktSystemBundle:Locale');
        $locales = $localeRepo->findAll();

        return $this->render('EgzaktSystemBundle:Backend/Navigation:locale_bar.html.twig', array(
            'locales'    => $locales,
            'editLocale' => $this->getCore()->getEditLocale()
        ));
    }
}
