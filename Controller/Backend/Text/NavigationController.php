<?php

namespace Flexy\SystemBundle\Controller\Backend\Text;

use Flexy\SystemBundle\Lib\Backend\BaseController;
use Flexy\SystemBundle\Entity\Text;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{
    public function sectionModuleBarAction($_masterRoute)
    {
        $selected = (0 === strpos($_masterRoute, 'flexy_system_backend_text'));

        return $this->render('FlexySystemBundle:Backend/Text/Navigation:section_module_bar.html.twig', array(
            'selected' => $selected,
        ));
    }
}
