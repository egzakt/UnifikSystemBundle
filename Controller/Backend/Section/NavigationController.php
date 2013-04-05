<?php

namespace Egzakt\SystemBundle\Controller\Backend\Section;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{

    /**
     * Global Bundle Bar Action
     *
     * @param string $masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($masterRoute)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_section'));

        return $this->render('EgzaktSystemBundle:Backend/Section/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
