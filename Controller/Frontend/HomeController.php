<?php

namespace Egzakt\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Home Controller
 */
class HomeController extends Controller
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