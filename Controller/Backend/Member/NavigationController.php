<?php

namespace Egzakt\SystemBundle\Controller\Backend\Member;

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
     * @param string $masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($masterRoute)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_member'));

        return $this->render('EgzaktSystemBundle:Backend/Member/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }

}
