<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Lib\Frontend\BaseController;

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
        return $this->render('UnifikSystemBundle:Frontend/Home:index.html.twig');
    }
}
