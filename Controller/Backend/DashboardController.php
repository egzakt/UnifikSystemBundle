<?php

namespace Egzakt\SystemBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;

/**
 * Dashboard controller
 */
class DashboardController extends BaseController
{
    /**
     * Default action
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EgzaktSystemBundle:Backend/Dashboard:index.html.twig');
    }

    /**
     * Help action
     *
     * @return Response
     */
    public function helpAction()
    {
        return $this->render('EgzaktSystemBundle:Backend/Dashboard:help.html.twig');
    }

    /**
     * Help detail action
     *
     * @param string $itemId
     *
     * @return Response
     */
    public function helpDetailAction($itemId)
    {
        return $this->render('EgzaktSystemBundle:Backend/Dashboard:help.html.twig', array(
            'itemId' => $itemId
        ));
    }
}
