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
    }

    /**
     * Breadcrumbs Action
     *
     * @return Response
     */
    public function breadcrumbsAction()
    {
//        $elementCurrent = $this->getCore()->getElement();
//        $elements = $this->get('egzakt_system.breadcrumbs')->getElements();
//
//        return $this->render('EgzaktSystemBundle:Backend/Navigation:breadcrumbs.html.twig', array(
//            'elements' => $elements,
//            'elementCurrent' => $elementCurrent
//        ));
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

}
