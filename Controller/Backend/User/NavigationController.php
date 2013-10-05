<?php

namespace Flexy\SystemBundle\Controller\Backend\User;

use Flexy\SystemBundle\Lib\Backend\BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * User Controller
 */
class NavigationController extends BaseController
{
    /**
     * Global Bundle Bar Action
     *
     * @param string $_masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($_masterRoute)
    {
        // Access restricted to ROLE_BACKEND_ADMIN
        if (false === $this->get('security.context')->isGranted('ROLE_BACKEND_ADMIN')) {
            return new Response();
        }

        $selected = (0 === strpos($_masterRoute, 'flexy_system_backend_user'));

        return $this->render('FlexySystemBundle:Backend/User/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
