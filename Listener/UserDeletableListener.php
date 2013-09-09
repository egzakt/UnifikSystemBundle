<?php

namespace Egzakt\SystemBundle\Listener;

use Egzakt\SystemBundle\Lib\BaseDeletableListener;
use Egzakt\SystemBundle\Lib\BaseEntity;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserDeletableListener extends BaseDeletableListener
{

    private $security;

    public function __construct(SecurityContextInterface $sci)
    {
        parent::__construct();

        $this->security = $sci;
    }

    protected function getCurrentUser()
    {
        return $this->security->getToken()->getUser();
    }

    public function isDeletable(BaseEntity $entity) {

        if ($this->getCurrentUser()->getId() == $entity->getId()) {
            $this->addError('You can\'t delete yourself.');
        }

        return $this->validate();

    }

}


