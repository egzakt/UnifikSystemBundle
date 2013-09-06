<?php

namespace Egzakt\SystemBundle\Controller\Backend\Role;

use Egzakt\SystemBundle\Lib\Backend\BaseController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

/**
 * Role Navigation Controller
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

        $selected = (0 === strpos($_masterRoute, 'egzakt_system_backend_role'));

        return $this->render('EgzaktSystemBundle:Backend/Role/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }

    /**
     * Impersonating Bar
     *
     * Allows you to switch back to the Original Token
     *
     * @return Response
     */
    public function impersonatingBarAction()
    {
        $securityContext = $this->get('security.context');

        // Make sure you're impersonating a User
        if (!$securityContext->isGranted('ROLE_PREVIOUS_ADMIN')) {
            return new Response();
        }

        $previousToken = null;

        // Loop through the Roles
        foreach ($securityContext->getToken()->getRoles() as $role) {

            // If it's a SwitchUserRole instance, we can get back the Original Token
            if ($role instanceof SwitchUserRole) {
                $previousToken = $role->getSource();
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Role/Navigation:impersonating_bar.html.twig', array(
            'previousToken' => $previousToken
        ));
    }
}
