<?php

namespace Unifik\SystemBundle\Controller\Backend\Text;

use Unifik\SystemBundle\Lib\Backend\BaseController;
use Unifik\SystemBundle\Entity\Text;

/**
 * Navigation controller.
 *
 */
class NavigationController extends BaseController
{
    public function sectionModuleBarAction($_masterRoute)
    {
        $selected = (0 === strpos($_masterRoute, 'unifik_system_backend_text'));

        return $this->render('UnifikSystemBundle:Backend/Text/Navigation:section_module_bar.html.twig', array(
            'selected' => $selected,
        ));
    }
}
