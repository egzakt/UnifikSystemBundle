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
     * @return Response
     */
    public function globalModuleBarAction()
    {
        return $this->render('EgzaktSystemBundle:Backend/Member/Navigation:global_bundle_bar.html.twig');
    }

}
