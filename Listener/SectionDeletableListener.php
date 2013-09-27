<?php

namespace Egzakt\SystemBundle\Listener;

use Egzakt\SystemBundle\Lib\BaseDeletableListener;

class SectionDeletableListener extends BaseDeletableListener
{
    /**
     * @inheritedDoc
     */
    public function isDeletable($entity)
    {
        if (count($entity->getChildren()) > 0) {
            $this->addError('This section has one or more subsections.');
        }

        return $this->validate();
    }
}
