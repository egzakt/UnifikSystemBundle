<?php

namespace Flexy\SystemBundle\Lib\Frontend;

use Flexy\SystemBundle\Lib\ApplicationController;

/**
 * Base Controller for all Flexy Frontend Bundles
 */
abstract class BaseController extends ApplicationController
{


    /**
     * Get the frontend core
     *
     * @return Core
     */
    public function getCore()
    {
        return $this->get('flexy_frontend.core');
    }


}
