<?php

namespace Egzakt\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Frontend\BaseController;

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

        if ($element) {
            $headExtra = $element->getHeadExtra();
        } else {
            $headExtra = '';
        }

        return $this->render('EgzaktSystemBundle:Frontend/Core:head_extra.html.twig', array(
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
//            return $this->render('EgzaktFrontendCoreBundle:Core:robots_prod.txt.twig');
//        }
//        else {
//            return $this->render('EgzaktFrontendCoreBundle:Core:robots_dev.txt.twig');
//        }
    }
}
