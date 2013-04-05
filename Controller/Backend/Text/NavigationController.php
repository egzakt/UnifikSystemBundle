<?php

namespace Egzakt\SystemBundle\Controller\Backend\Text;

use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Text;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{
    public function bundleBarAction($masterRoute, $section)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_text'));

        return $this->render('EgzaktSystemBundle:Backend/Text/Navigation:bundle_bar.html.twig', array(
            'selected' => $selected,
            'section' => $section
        ));
    }

    /**
     * Global Bundle Bar Action
     *
     * @param string $masterRoute
     *
     * @return Response
     */
    public function globalModuleBarAction($masterRoute)
    {
        $selected = (0 === strpos($masterRoute, 'egzakt_system_backend_text'));

        return $this->render('EgzaktSystemBundle:Backend/Text/Navigation:global_bundle_bar.html.twig', array(
            'selected' => $selected
        ));
    }
}
