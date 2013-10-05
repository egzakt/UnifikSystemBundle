<?php

namespace Flexy\SystemBundle\Lib;

use Flexy\SystemBundle\Entity\Section;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class SectionFilter
 */
class SectionFilter
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var array
     */
    protected $allowedSectionIds;

    /**
     * Constructor
     *
     * @param EntityManager            $entityManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(EntityManager $entityManager, SecurityContextInterface $securityContext)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
    }

    /**
     * Can Access
     *
     * Check if the current User has access to this Section
     *
     * @param int|Section $sectionId
     *
     * @return bool
     */
    public function canAccess($sectionId)
    {
        if (is_object($sectionId)) {
            $sectionId = $sectionId->getId();
        }

        $allowedSectionIds = $this->getAllowedSectionIds();

        return in_array($sectionId, $allowedSectionIds);
    }

    /**
     * Filter Sections
     *
     * Remove inaccessible childrens and override the parent's route if the parent is inaccessible
     *
     * @param array $sections
     * @param null  $parent
     *
     * @return array
     */
    public function filterSections(Array $sections, $parent = null)
    {
        if ($this->securityContext->isGranted('ROLE_BACKEND_ADMIN')) {
            return $sections;
        }

        foreach ($sections as $key => $section) {

            // Go to the deepest children
            if ($section->hasChildren()) {
                // Filter these childrens
                $this->filterSections($section->getChildren(), $section);
            }

            // If this section is not accessible, remove it (from the root or from the parent)
            if (!$this->canAccess($section->getEntity())) {
                if ($parent) {
                    // Children Section
                    $parent->removeChildren($section);
                } else {
                    // Root Sections
                    unset($sections[$key]);
                }

                // Section has been removed, don't go much further
                continue;
            }

            // This route will be applied to all inaccessible parents
            $route = $section->getRoute();
            $routeParams = $section->getRouteParams();

            $sectionParent = $section->getParent();

            // For each inaccessible parent, override it's route/routeParams with the children's one
            while ($sectionParent && !$this->canAccess($sectionParent->getEntity())) {
                $sectionParent->getEntity()->setRoute($route);
                $sectionParent->getEntity()->setRouteParams($routeParams);

                // Mark this parent Section as accessible because it's children is accessible
                if (!in_array($sectionParent->getEntity()->getId(), $this->allowedSectionIds)) {
                    $this->allowedSectionIds[] = $sectionParent->getEntity()->getId();
                }

                $sectionParent = $sectionParent->getParent();
            }
        }

        return $sections;
    }

    /**
     * Get Allowed Section Ids
     *
     * @return array
     */
    protected function getAllowedSectionIds()
    {
        if (null !== $this->allowedSectionIds) {
            return $this->allowedSectionIds;
        }

        $this->allowedSectionIds = array();

        // Get the token
        $token = $this->securityContext->getToken();

        if ($token->isAuthenticated()) {

            $roles = array();
            foreach ($token->getRoles() as $role) {
                $roles[] = $role->getRole();
            }

            // Find the Sections for the User's Roles
            $this->allowedSectionIds = $this->entityManager->getRepository('FlexySystemBundle:Section')->findHavingRoles($roles);
        }

        return $this->allowedSectionIds;
    }

}
