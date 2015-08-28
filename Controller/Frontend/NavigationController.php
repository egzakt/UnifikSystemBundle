<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Entity\AppRepository;
use Unifik\SystemBundle\Entity\NavigationRepository;
use Unifik\SystemBundle\Entity\SectionNavigationRepository;
use Unifik\SystemBundle\Lib\Frontend\BaseController;
use Unifik\SystemBundle\Entity\MappingRepository;
use Unifik\SystemBundle\Entity\SectionRepository;

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
     * @var SectionNavigationRepository
     */
    protected $sectionNavigationRepository;

    /**
     * @var MappingRepository
     */
    protected $mappingRepository;

    /**
     * @var NavigationRepository
     */
    protected $navigationRepository;

    /**
     * @var AppRepository
     */
    protected $appRepository;

    /**
     * Init
     */
    public function init()
    {
        $this->sectionRepository = $this->getEm()->getRepository('UnifikSystemBundle:Section');
        $this->sectionNavigationRepository = $this->getEm()->getRepository('UnifikSystemBundle:SectionNavigation');
        $this->mappingRepository = $this->getEm()->getRepository('UnifikSystemBundle:Mapping');
        $this->navigationRepository = $this->getEm()->getRepository('UnifikSystemBundle:Navigation');
        $this->appRepository = $this->getEm()->getRepository('UnifikSystemBundle:App');
    }

    /**
     * Render a navigation using the navigation code as the fetch criteria
     *
     * @param Request $request The Request
     * @param string  $code     The navigation code
     * @param int     $maxLevel The level maximum limit, this is the rendering loop level limit, not the section entity level
     * @param bool    $exploded When false only the currently selected tree path is displayed
     * @param string  $template Force the template code to use
     * @param array   $attr     Array of attribure to add to the element (Ex. id="aaa" class="bbb")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function byCodeAction(Request $request, $code, $appId = null, $maxLevel = 10, $exploded = false, $template = '', $excludeHome = false, $attr = array())
    {
        // Cache
        $response = new Response();
        $response->setPublic();

        $sectionLastUpdate = $this->sectionRepository->findLastUpdate();
        $sectionNavigationLastUpdate = $this->sectionNavigationRepository->findLastUpdate();

        $response->setEtag($sectionLastUpdate . $sectionNavigationLastUpdate);

        if ($response->isNotModified($request)) {
            return $response;
        }

        // Rebuild the cache
        $appId = ($appId === null) ? $this->getApp()->getId() : $appId;
        $navigation = $this->navigationRepository->findOneByCodeAndApp($code, $appId);

        if (false == $navigation) {
            throw new \Exception('Can\'t find a navigation entity using code "' . $code . '"');
        }

        $sections = $this->sectionRepository->findByNavigationAndApp($navigation->getId(), $appId);
        $sections = new ArrayCollection($sections);
        if ($excludeHome) {
            foreach ($sections as $section) {
                if ($section->isHomeSection()) {
                    $sections->removeElement($section);
                }
            }
        }

        $template = ($template ? '_' . $template : '');

        $navigationBuilder = $this->get('unifik_system.navigation_builder');
        $navigationBuilder->setElements($sections, true, $maxLevel);
        $navigationBuilder->setSelectedElement($this->getCore()->getSection());
        $navigationBuilder->build();

        $elements = $navigationBuilder->getElements();

        return $this->render(
            'UnifikSystemBundle:Frontend/Navigation:by_code' . $template . '.html.twig',
            array(
                'code' => $code,
                'sections' => $elements,
                'maxLevel' => $maxLevel,
                'currentSection' => $this->getSection(),
                'attr' => $attr,
                'exploded' => $exploded
            ),
            $response
        );
    }

    /**
     * Render a navigation displaying children starting from a section
     *
     * @param Request $request The Request
     * @param mixed   $section  The section entity or section id to start from
     * @param int     $maxLevel The level maximum limit, this is the rendering loop level limit, not the section entity level
     * @param bool    $exploded When false only the currently selected tree path is displayed
     * @param string  $template Force the template code to use
     * @param array   $attr     Array of attribure to add to the element (Ex. id="aaa" class="bbb")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function fromSectionAction(Request $request, $section, $maxLevel = 10, $exploded = false, $template = '', $attr = [])
    {
        // Cache
        $response = new Response();
        $response->setPublic();

        $response->setEtag($this->sectionRepository->findLastUpdate());

        if ($response->isNotModified($request)) {
            return $response;
        }

        // Rebuild the cache
        if (is_numeric($section)) {
            $section = $this->sectionRepository->findOneWithChildren($section);
        }

        $elements = [];

        if ($parents = $section->getParents()) {
            $elements = $parents[1]->getChildren();
        }

        elseif (count($section->getChildren())){
            $elements = $section->getChildren();
        }

        $template = ($template ? '_' . $template : '');

        $navigationBuilder = $this->get('unifik_system.navigation_builder');
        $navigationBuilder->setElements($elements, true, $maxLevel);
        $navigationBuilder->setSelectedElement($this->getCore()->getSection());
        $navigationBuilder->build();

        $elements = $navigationBuilder->getElements();

        return $this->render(
            'UnifikSystemBundle:Frontend/Navigation:from_section' . $template . '.html.twig',
            array(
                'sections' => $elements,
                'maxLevel' => $maxLevel,
                'currentSection' => $this->getSection(),
                'attr' => $attr,
                'exploded' => $exploded
            ),
            $response
        );
    }

    public function appNavigationAction(Request $request, $template = '', $code = 'primary', $maxLevel = 10, $exploded = false, $exceptIds = array(), $attr = array())
    {
        // Cache
        $response = new Response();
        $response->setPublic();

        $appLastUpdate = $this->appRepository->findLastUpdate();
        $sectionLastUpdate = $this->sectionRepository->findLastUpdate();
        $sectionNavigationLastUpdate = $this->sectionNavigationRepository->findLastUpdate();

        $response->setEtag($appLastUpdate . $sectionLastUpdate . $sectionNavigationLastUpdate);

        if ($response->isNotModified($request)) {
            return $response;
        }

        // Rebuild the cache
        $exceptIds = (!is_array($exceptIds)) ? array($exceptIds) : $exceptIds;
        $exceptIds = array_merge($exceptIds, [AppRepository::BACKEND_APP_ID, AppRepository::FRONTEND_APP_ID]);
        $apps = $this->appRepository->findAllForNavigation($code, $exceptIds);

        $template = ($template ? '_' . $template : '');

        $navigationBuilder = $this->get('unifik_system.navigation_builder');
        $navigationBuilder->setElements($apps);
        if ($this->getSection()->isHomeSection()) {
            $navigationBuilder->setSelectedElement($this->getCore()->getSection()->getApp());
        } else {
            $navigationBuilder->setSelectedElement($this->getCore()->getSection());
        }
        $navigationBuilder->build();

        $elements = $navigationBuilder->getElements();

        return $this->render(
            'UnifikSystemBundle:Frontend/Navigation:app_nav' . $template . '.html.twig',
            array(
                'code' => 'apps',
                'sections' => $elements,
                'maxLevel' => $maxLevel,
                'currentSection' => $this->getSection(),
                'attr' => $attr,
                'exploded' => $exploded
            ),
            $response);
    }

    /**
     * Breadcrumbs Action
     *
     * @return Response
     */
    public function breadcrumbsAction()
    {
        $elementCurrent = $this->getCore()->getElement();
        $elements = $this->get('unifik_system.breadcrumbs')->getElements();

        return $this->render('UnifikSystemBundle:Frontend/Navigation:breadcrumbs.html.twig', array(
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
        $elements = $this->get('unifik_system.page_title')->getElements();

        $elementPageTitle = null;
        $elementOverridePageTitle = null;

        if (count($elements)) {
            $currentElement = $elements[count($elements) - 1];
            $elementPageTitle = $this->get('unifik_doctrine_behaviors.metadatable_getter')->getMetadata($currentElement, 'title');
            $elementOverridePageTitle = $this->get('unifik_doctrine_behaviors.metadatable_getter')->getMetadata($currentElement, 'titleOverride');

            if ($elementPageTitle || $elementOverridePageTitle) {
                unset($elements[count($elements) - 1]);
            }
        }

        return $this->render('UnifikSystemBundle:Frontend/Navigation:page_title.html.twig', array(
            'element_page_title' => $elementPageTitle,
            'element_override_page_title' => $elementOverridePageTitle,
            'elements' => $elements
        ));
    }

    /**
     * Locale Switcher Action
     *
     * @return Response
     */
    public function localeSwitcherAction()
    {
        $response = new Response();
        $response->setPublic();

        $localeSwitcher = $this->get('unifik_system.locale_switcher');
        $localeSwitcher->setElement($this->getCore()->getElement());

        $routes = $localeSwitcher->generate();

        return $this->render(
            'UnifikSystemBundle:Frontend/Navigation:locale_switcher.html.twig',
            [
                'routes' => $routes,
            ],
            $response
        );
    }

}
