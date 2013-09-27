<?php

namespace Egzakt\SystemBundle\Listener;

use Egzakt\SystemBundle\Lib\BaseDeletableListener;

class RoleDeletableListener extends BaseDeletableListener
{
    /**
     * @inheritedDoc
     */
    public function isDeletable($entity)
    {
        if (in_array($entity->getRole(), array('ROLE_DEVELOPER', 'ROLE_BACKEND_ADMIN', 'ROLE_ADMIN'))) {
            $this->addError('You can\'t delete this Role.');
        }

        return $this->validate();
    }
}
