<?php

namespace Egzakt\SystemBundle\Controller\Backend\Section;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{

    public function sectionModuleBarAction($masterRoute, $section)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_section'));

        return $this->render('EgzaktSystemBundle:Backend/Section/Navigation:section_module_bar.html.twig', array(
            'selected' => $selected,
            'section' => $section
        ));
    }

    /**
     * Global Bundle Bar Action
     *
     * @param string $masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($masterRoute)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_section'));

        return $this->render('EgzaktSystemBundle:Backend/Section/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
