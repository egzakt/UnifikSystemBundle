<?php

namespace Egzakt\SystemBundle\Lib;

use Doctrine\Common\Collections\ArrayCollection;

interface DeletableListenerInterface
{
    /**
     * @param  BaseEntity $entity
     * @return boolean
     */
    public function isDeletable(BaseEntity $entity);

    /**
     * @return ArrayCollection
     */
    public function getErrors();

}
