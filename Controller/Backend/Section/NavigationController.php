<?php

namespace Flexy\SystemBundle\Controller\Backend\Section;

use Symfony\Component\HttpFoundation\Response;

use Flexy\SystemBundle\Lib\Backend\BaseController;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{

    public function sectionModuleBarAction($_masterRoute)
    {
        $selected = (0 === strpos($_masterRoute, 'flexy_system_backend_section'));

        return $this->render('FlexySystemBundle:Backend/Section/Navigation:section_module_bar.html.twig', array(
            'selected' => $selected
        ));
    }

    /**
     * Global Bundle Bar Action
     *
     * @param string $_masterRoute
     *
     * @return Response
     */
    public function appModuleBarAction($_masterRoute)
    {
        $selected = (0 === strpos($_masterRoute, 'flexy_system_backend_section'));

        return $this->render('FlexySystemBundle:Backend/Section/Navigation:app_module_bar.html.twig', array(
            'selected' => $selected,
            'managedApp' => $this->getApp()
        ));
    }
}
