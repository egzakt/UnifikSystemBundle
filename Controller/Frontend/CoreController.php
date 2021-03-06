<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Lib\Frontend\BaseController;

/**
 * Core Controller
 *
 */
class CoreController extends BaseController
{
    /**
     * Head extra Action
     *
     * @return Response
     */
    public function headExtraAction()
    {
        $element = $this->getCore()->getElement();

        $headExtra = '';

        if ($element) {
            if (method_exists($element, 'getHeadExtra')) {
                $headExtra = $element->getHeadExtra();
            }
        }

        return $this->render('UnifikSystemBundle:Frontend/Core:head_extra.html.twig', array(
            'headExtra' => $headExtra,
        ));
    }

    /**
     * Head extra Action
     *
     * @return Response
     */
    public function robotsAction()
    {
//        if ($this->container->get('kernel')->getEnvironment() == 'prod') {
//            return $this->render('UnifikFrontendCoreBundle:Core:robots_prod.txt.twig');
//        }
//        else {
//            return $this->render('UnifikFrontendCoreBundle:Core:robots_dev.txt.twig');
//        }
    }
}
