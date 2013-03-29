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
     * @return Response
     */
    public function globalModuleBarAction()
    {
        return $this->render('EgzaktSystemBundle:Backend/User/Navigation:global_bundle_bar.html.twig', array(
        ));
    }

}
