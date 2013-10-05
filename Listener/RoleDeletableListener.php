<?php

namespace Flexy\SystemBundle\Listener;

use Flexy\SystemBundle\Lib\BaseDeletableListener;

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

        if (count($entity->getUsers()) > 0) {
            $this->addError('This role has one or more users.');
        }

        return $this->validate();
    }
}
