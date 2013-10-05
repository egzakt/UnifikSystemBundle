<?php

namespace Flexy\SystemBundle\Controller\Backend\Application;

use Symfony\Component\HttpFoundation\Response;

use Flexy\SystemBundle\Lib\Backend\BaseController;

/**
 * Navigation controller
 */
class NavigationController extends BaseController
{
    /**
     * Global module bar action
     *
     * @param string $_masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($_masterRoute)
    {
        $selected = (0 === strpos($_masterRoute, 'flexy_system_backend_application'));

        return $this->render('FlexySystemBundle:Backend/Application/Navigation:global_module_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
