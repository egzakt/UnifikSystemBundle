<?php

namespace Unifik\SystemBundle\Controller\Backend\Locale;

use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Lib\Backend\BaseController;

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

        $selected = (0 === strpos($_masterRoute, 'unifik_system_backend_locale'));

        return $this->render('UnifikSystemBundle:Backend/Locale/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }

}
