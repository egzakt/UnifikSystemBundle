<?php

namespace Unifik\SystemBundle\Controller\Backend;

use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Lib\Backend\BaseController;

/**
 * Javascripts controller
 */
class JavascriptsController extends BaseController
{
    /**
     * Default action
     *
     * @return Response
     */
    public function quickCreateAction()
    {
        return $this->render('UnifikSystemBundle:Backend/Javascripts:quick_create.js.twig');
    }
}
