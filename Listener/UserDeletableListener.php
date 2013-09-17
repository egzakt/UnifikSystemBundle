<?php

namespace Egzakt\SystemBundle\Listener;

use Egzakt\SystemBundle\Entity\User;
use Egzakt\SystemBundle\Lib\BaseDeletableListener;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserDeletableListener extends BaseDeletableListener
{

    /**
     * @var SecurityContextInterface
     */
    private $security;

    public function __construct(SecurityContextInterface $sci)
    {
        $this->security = $sci;
    }

    /**
     * @return User
     */
    protected function getCurrentUser()
    {
        return $this->security->getToken()->getUser();
    }

    /**
     * @inheritedDoc
     */
    public function isDeletable($entity)
    {
        if ($this->getCurrentUser()->getId() == $entity->getId()) {
            $this->addError('You can\'t delete yourself.');
        }

        return $this->validate();
    }

}
