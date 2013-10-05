<?php

namespace Flexy\SystemBundle\Listener;

use Flexy\SystemBundle\Entity\AppRepository;
use Flexy\SystemBundle\Lib\BaseDeletableListener;

class AppDeletableListener extends BaseDeletableListener
{
    /**
     * @inheritedDoc
     */
    public function isDeletable($entity)
    {
        $restrictedApps = array(AppRepository::FRONTEND_APP_ID, AppRepository::BACKEND_APP_ID);

        foreach ($restrictedApps as $restrictedApp) {
            if ($restrictedApp == $entity->getId()) {
                $this->addError('This application can\'t be deleted.');
            }
        }

        return $this->validate();
    }
}
