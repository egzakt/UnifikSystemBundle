<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Entity\NavigationRepository;
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
     * @var MappingRepository
     */
    protected $mappingRepository;

    /**
     * @var NavigationRepository
     */
    protected $navigationRepository;

    /**
     * Init
     */
    public function init()
    {
        $this->sectionRepository = $this->getEm()->getRepository('UnifikSystemBundle:Section');
        $this->mappingRepository = $this->getEm()->getRepository('UnifikSystemBundle:Mapping');
        $this->navigationRepository = $this->getEm()->getRepository('UnifikSystemBundle:Navigation');
    }

    /**
     * Render a navigation using the navigation code as the fetch criteria
     *
     * @param string $code     The navigation code
     * @param int    $maxLevel The level maximum limit, this is the rendering loop level limit, not the section entity level
     * @param bool   $exploded When false only the currently selected tree path is displayed
     * @param string $template Force the template code to use
     * @param array  $attr     Array of attribure to add to the element (Ex. id="aaa" class="bbb")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function byCodeAction($code, $maxLevel = 10, $exploded = false, $template = '', $attr = array())
    {
        $app_id = $this->getApp()->getId();
        $navigation = $this->navigationRepository->findOneByCodeAndApp($code, $app_id);

        if (false == $navigation) {
            throw new \Exception('Can\'t find a navigation entity using code "' . $code . '"');
        }

        $sections = $this->sectionRepository->findByNavigationAndApp($navigation->getId(), $app_id);

        $template = ($template ? '_' . $template : '');

        $navigationBuilder = $this->get('unifik_system.navigation_builder');
        $navigationBuilder->setElements($sections);
        $navigationBuilder->setSelectedElement($this->getCore()->getSection());
        $navigationBuilder->build();

        $elements = $navigationBuilder->getElements();

        return $this->render('UnifikSystemBundle:Frontend/Navigation:by_code' . $template . '.html.twig', array(
            'code' => $code,
            'sections' => $elements,
            'maxLevel' => $maxLevel,
            'currentSection' => $this->getSection(),
            'attr' => $attr,
            'exploded' => $exploded
        ));
    }

    /**
     * Render a navigation displaying children starting from a section
     *
     * @param mixed  $section  The section entity or section id to start from
     * @param int    $maxLevel The level maximum limit, this is the rendering loop level limit, not the section entity level
     * @param bool   $exploded When false only the currently selected tree path is displayed
     * @param string $template Force the template code to use
     * @param array  $attr     Array of attribure to add to the element (Ex. id="aaa" class="bbb")
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function fromSectionAction($section, $maxLevel = 10, $exploded = false, $template = '', $attr = [])
    {
        if (is_numeric($section)) {
            $section = $this->sectionRepository->find($section);
        }

        $elements = [];

        if ($parents = $section->getParents()) {
            $elements = $parents[1]->getChildren();
        }

        $template = ($template ? '_' . $template : '');

        $navigationBuilder = $this->get('unifik_system.navigation_builder');
        $navigationBuilder->setElements($elements);
        $navigationBuilder->setSelectedElement($this->getCore()->getSection());
        $navigationBuilder->build();

        $elements = $navigationBuilder->getElements();

        return $this->render('UnifikSystemBundle:Frontend/Navigation:from_section' . $template . '.html.twig', array(
            'sections' => $elements,
            'maxLevel' => $maxLevel,
            'currentSection' => $this->getSection(),
            'attr' => $attr,
            'exploded' => $exploded
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

        return $this->render('UnifikSystemBundle:Backend/Navigation:page_title.html.twig', array(
            'elements' => $elements,
        ));
    }

    /**
     * Locale Switcher Action
     *
     * @return Response
     */
    public function localeSwitcherAction()
    {
        $localeSwitcher = $this->get('unifik_system.locale_switcher');
        $localeSwitcher->setElement($this->getCore()->getElement());

        $routes = $localeSwitcher->generate();

        return $this->render(
            'UnifikSystemBundle:Frontend/Navigation:locale_switcher.html.twig',
            array(
                'routes' => $routes,
            )
        );
    }

}
