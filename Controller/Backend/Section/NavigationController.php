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

    public function sectionModuleBarAction($masterRoute)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_section'));

        return $this->render('EgzaktSystemBundle:Backend/Section/Navigation:section_module_bar.html.twig', array(
            'selected' => $selected
        ));
    }

    /**
     * Global Bundle Bar Action
     *
     * @param string $appSlug
     * @param string $masterRoute
     *
     * @return Response
     */
    public function appModuleBarAction($appSlug, $masterRoute)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_section'));

        return $this->render('EgzaktSystemBundle:Backend/Section/Navigation:app_module_bar.html.twig', array(
            'selected' => $selected,
            'appSlug' => $appSlug
        ));
    }
}
