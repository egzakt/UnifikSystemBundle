<?php

namespace Egzakt\SystemBundle\Lib\Backend;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

/**
 * Class CmsVoter
 */
class CmsVoter implements VoterInterface {

    /**
     * @var ContainerAwareInterface
     *
     * Container is used because we cannot inject the security.context to get the Token (ServiceCircularReferenceException)
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * Only support ROLE_ attributes
     */
    public function supportsAttribute($attribute)
    {
        return 0 === strpos($attribute, 'ROLE_BACKEND_');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * Vote
     *
     * This function is automatically called by the framework
     *
     * You can call it manually within a Controller with an $object/$attributes as argument
     *
     * The default $attributes will be the roles required for the current URL
     *
     * @param TokenInterface $token
     * @param object $object
     * @param array $attributes
     *
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {

            // Check if this Voter supports this Role
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            // Get the Role Hierarchy
            $roleHierarchy = new RoleHierarchy($this->container->getParameter('security.role_hierarchy.roles'));

            // Get all the grantes roles from the Hierarchy
            $grantedRoles = $roleHierarchy->getReachableRoles($token->getRoles());

            // ROLE_ADMIN has full access
            // Can't use ->isGranted because this method uses the Voters = (infinite loop)!
            foreach($grantedRoles as $grantedRole) {
                if ($grantedRole->getRole() == 'ROLE_BACKEND_ADMIN') {
                    return VoterInterface::ACCESS_GRANTED;
                }
            }

            // Get the current route
            // Need to use a Try Catch because subrequests (_fragment) can be voted...
            try {
                $route = $this->container->get('router')->match($this->container->get('request')->getPathInfo());
            } catch (ResourceNotFoundException $e) {
                continue;
            }

            // If there is a section_id parameter in the Route
            if (array_key_exists('sectionId', $route)) {

                // Check is the user can access this Section
                if ($this->container->get('egzakt_system.section_filter')->canAccess($route['sectionId'])) {
                    return VoterInterface::ACCESS_GRANTED;
                } else {
                    $result = VoterInterface::ACCESS_DENIED;
                }
            }
        }

        return $result;
    }

}