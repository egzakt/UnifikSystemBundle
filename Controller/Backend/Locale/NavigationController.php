<?php

namespace Flexy\SystemBundle\Controller\Backend\Locale;

use Symfony\Component\HttpFoundation\Response;

use Flexy\SystemBundle\Lib\Backend\BaseController;

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

        $selected = (0 === strpos($_masterRoute, 'flexy_system_backend_locale'));

        return $this->render('FlexySystemBundle:Backend/Locale/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }

}
