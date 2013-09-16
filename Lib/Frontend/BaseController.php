<?php

namespace Egzakt\SystemBundle\Lib\Frontend;

use Egzakt\SystemBundle\Lib\ApplicationController;

/**
 * Base Controller for all Egzakt Frontend Bundles
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
        return $this->get('egzakt_frontend.core');
    }


}
