<?php

namespace Egzakt\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Frontend\BaseController;

/**
 * Home Controller
 */
class HomeController extends BaseController
{
    /**
     * Index Action
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EgzaktSystemBundle:Frontend/Home:index.html.twig');
    }
}
