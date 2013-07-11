<?php

namespace Egzakt\SystemBundle\Controller\Backend\User;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * User Controller
 */
class NavigationController extends BaseController
{
    /**
     * Global Bundle Bar Action
     *
     * @param string $masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($masterRoute)
    {
        // Access restricted to ROLE_BACKEND_ADMIN
        if (false === $this->get('security.context')->isGranted('ROLE_BACKEND_ADMIN')) {
            return new Response();
        }

        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_user'));

        return $this->render('EgzaktSystemBundle:Backend/User/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
