<?php

namespace Egzakt\SystemBundle\Controller\Backend\Locale;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;

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
        $selected = (0 === strpos($_masterRoute, 'egzakt_system_backend_locale'));

        return $this->render('EgzaktSystemBundle:Backend/Locale/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }

}
