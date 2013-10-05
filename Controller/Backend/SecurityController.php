<?php

namespace Flexy\SystemBundle\Controller\Backend;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Flexy\SystemBundle\Entity\Login;
use Flexy\SystemBundle\Lib\Backend\BaseController;

/**
 * Security Controller
 */
class SecurityController extends BaseController
{
    /**
     * Backend main login form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // Get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->set(SecurityContext::AUTHENTICATION_ERROR, '');
        }

        // Last username entered by the user
        $lastUsername = $session->get(SecurityContext::LAST_USERNAME);

        // Each login attempt is logged
        if (isset($_SERVER['REMOTE_ADDR']) && $error && $lastUsername) {
            $this->logLoginAttempt($lastUsername);
        }

        return $this->render('FlexySystemBundle:Backend/Security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * Log a failed login attempt
     *
     * @param string $username
     */
    protected function logLoginAttempt($username)
    {
        $login = new Login();
        $login->setIp($this->getRequest()->getClientIp());
        $login->setUsername($username);
        $login->setSuccess(false);

        $this->getEm()->persist($login);
        $this->getEm()->flush();
    }

}
