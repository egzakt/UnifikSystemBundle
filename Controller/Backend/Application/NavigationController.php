<?php

namespace Unifik\SystemBundle\Controller\Backend\Application;

use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Lib\Backend\BaseController;

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
        $selected = (0 === strpos($_masterRoute, 'unifik_system_backend_application'));

        return $this->render('UnifikSystemBundle:Backend/Application/Navigation:global_module_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
