<?php

namespace Flexy\SystemBundle\Listener;

use Flexy\SystemBundle\Entity\User;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginBackendListener
{
    /**
     * Forcing the request locale if the user entity uses a custom locale that is set on the entity
     *
     * @param InteractiveLoginEvent $event
     */
    public function onLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        // Skipping login that is not coming from the backend User entity
        if (false == $user instanceof User) {
            return;
        }

        if ($locale = $user->getLocale()) {
            $event->getRequest()->setLocale($locale);
        }
    }
}
