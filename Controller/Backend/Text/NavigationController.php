<?php

namespace Egzakt\SystemBundle\Controller\Backend\Text;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Text;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{
    public function sectionModuleBarAction($_masterRoute)
    {
        $selected = (0 === strpos($_masterRoute, 'egzakt_system_backend_text'));

        return $this->render('EgzaktSystemBundle:Backend/Text/Navigation:section_module_bar.html.twig', array(
            'selected' => $selected,
        ));
    }
}
