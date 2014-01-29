<?php

namespace Unifik\SystemBundle\Lib\Frontend;

use Unifik\SystemBundle\Lib\ApplicationController;

/**
 * Base Controller for all Unifik Frontend Bundles
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
        return $this->get('unifik_frontend.core');
    }


}
