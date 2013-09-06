<?php

namespace Egzakt\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Frontend\BaseController;
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
        $this->navigationRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation');
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
        $navigation = $this->navigationRepository->findOneByCode($code);

        if (false == $navigation) {
            throw new \Exception('Can\'t find a navigation entity using code "' . $code . '"');
        }

        $sections = $this->sectionRepository->findByNavigationAndApp($navigation->getId(), 2);

        $template = ($template ? '_' . $template : '');

        $navigationBuilder = $this->get('egzakt_system.navigation_builder');
        $navigationBuilder->setElements($sections);
        $navigationBuilder->setSelectedElement($this->getCore()->getSection());
        $navigationBuilder->build();

        $elements = $navigationBuilder->getElements();

        return $this->render('EgzaktSystemBundle:Frontend/Navigation:by_code' . $template . '.html.twig', array(
            'code' => $code,
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
        $elements = $this->get('egzakt_system.breadcrumbs')->getElements();

        return $this->render('EgzaktSystemBundle:Frontend/Navigation:breadcrumbs.html.twig', array(
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
     * Locale Switcher Action
     *
     * @return Response
     */
    public function localeSwitcherAction()
    {
        $localeSwitcher = $this->get('egzakt_system.locale_switcher');
        $localeSwitcher->setElement($this->getCore()->getElement());

        $routes = $localeSwitcher->generate();

        return $this->render(
            'EgzaktSystemBundle:Frontend/Navigation:locale_switcher.html.twig',
            array(
                'routes' => $routes,
            )
        );
    }

}
