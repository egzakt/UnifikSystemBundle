<?php

namespace Egzakt\SystemBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\MappingRepository;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\SectionRepository;

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
     * @var MappingRepository
     */
    protected $mappingRepository;

    /**
     * Init
     */
    public function init()
    {
        $this->sectionRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Section');
        $this->mappingRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Mapping');
    }

    /**
     * Section Bar Action
     *
     * @return Response
     */
    public function sectionBarAction()
    {
        $sectionCurrent = $this->getSection();

        if (false == $sectionCurrent) {
            $sectionCurrent = new Section();
        }

        $sections = $this->sectionRepository->findByAppJoinChildren($this->getApp());

        // Cleanup of level 1 sections
        foreach ($sections as $key => $section) {
            if ($section->getParent()) {
                unset($sections[$key]);
            }
        }

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

        $appRepo = $this->getDoctrine()->getRepository('EgzaktSystemBundle:App');
        $apps = $appRepo->findBy(array(), array('ordering' => 'asc'));

        // BC fix, previous version had a "backend" application that need to be removed
        foreach ($apps as $key => $app) {
            if ($app->getName() == 'backend') {
                unset($apps[$key]);
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Navigation:app.html.twig', array(
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
        $elements = $this->get('egzakt_system.breadcrumbs')->getElements();

        return $this->render('EgzaktSystemBundle:Backend/Navigation:breadcrumbs.html.twig', array(
            'elements' => $elements,
            'elementCurrent' => $elementCurrent
        ));
    }

    /**
     * Page Title Action
     *
     * @return Response
     */
    public function pageTitleAction()
    {
        $elements = $this->get('egzakt_system.page_title')->getElements();

        return $this->render('EgzaktSystemBundle:Backend/Navigation:page_title.html.twig', array(
            'elements' => $elements,
        ));
    }

    /**
     * This render the modules that are associated with the current section
     *
     * @param $masterRoute
     *
     * @return Response
     */
    public function sectionModuleBarAction($masterRoute)
    {
        if (false == $this->getSection()) {
            return new Response();
        }

        $mappings = $this->mappingRepository->findBy(array(
            'section' => $this->getSection(),
            'navigation' => 2, // _section_module_bar
        ));

        return $this->render('EgzaktSystemBundle:Backend/Navigation:section_module_bar.html.twig', array(
            'mappings' => $mappings,
            'masterRoute' => $masterRoute,
        ));
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
            'locales' => $locales,
            'editLocale' => $this->getCore()->getEditLocale()
        ));
    }
}
