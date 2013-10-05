<?php

namespace Flexy\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Flexy\SystemBundle\Lib\Frontend\BaseController;

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
        return $this->render('FlexySystemBundle:Frontend/Home:index.html.twig');
    }
}
