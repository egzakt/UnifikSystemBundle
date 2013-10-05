<?php

namespace Flexy\SystemBundle\Listener;

use Flexy\SystemBundle\Entity\User;
use Flexy\SystemBundle\Lib\BaseDeletableListener;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserDeletableListener extends BaseDeletableListener
{
    /**
     * @var SecurityContextInterface
     */
    private $security;

    /**
     * Constructor
     *
     * @param SecurityContextInterface $sci
     */
    public function __construct(SecurityContextInterface $sci)
    {
        $this->security = $sci;
    }

    /**
     * Get the current User from the Security Context
     *
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
