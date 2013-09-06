<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Egzakt\SystemBundle\Entity\User;
use Egzakt\SystemBundle\Entity\RoleTranslation;
use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Role
 */
class Role extends BaseEntity implements RoleInterface, \Serializable
{
    /**
     * @var string $role
     */
    protected $role;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var ArrayCollection
     */
    protected $users;

    /**
     * @var ArrayCollection
     */
    protected $sections;

    /**
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New role';
        }

        if ($name = $this->name) {
            return $name;
        }

        // No translation found in the current locale
        return '';
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * Get Role
     *
     * @return null|string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add users
     *
     * @param User $users
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add sections
     *
     * @param  \Egzakt\SystemBundle\Entity\Section $sections
     * @return Role
     */
    public function addSection(\Egzakt\SystemBundle\Entity\Section $sections)
    {
        $this->sections[] = $sections;

        return $this;
    }

    /**
     * Set Sections
     *
     * @param ArrayCollection $sections
     */
    public function setSections(ArrayCollection $sections)
    {
        $this->sections = $sections;
    }

    /**
     * Remove sections
     *
     * @param \Egzakt\SystemBundle\Entity\Section $sections
     */
    public function removeSection(\Egzakt\SystemBundle\Entity\Section $sections)
    {
        $this->sections->removeElement($sections);
    }

    /**
     * Get sections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Add translations
     *
     * @param RoleTranslation $translations
     */
    public function addRoleTranslation(RoleTranslation $translations)
    {
        $this->translations[] = $translations;
    }

    /**
     * Get translations
     *
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add translations
     *
     * @param  \Egzakt\SystemBundle\Entity\RoleTranslation $translations
     * @return Role
     */
    public function addTranslation(\Egzakt\SystemBundle\Entity\RoleTranslation $translations)
    {
        $this->translations[] = $translations;

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \Egzakt\SystemBundle\Entity\RoleTranslation $translations
     */
    public function removeTranslation(\Egzakt\SystemBundle\Entity\RoleTranslation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Remove users
     *
     * @param \Egzakt\SystemBundle\Entity\User $users
     */
    public function removeUser(\Egzakt\SystemBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get the backend route
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getRouteBackend($suffix = 'edit')
    {
        return 'egzakt_system_backend_role_' . $suffix;
    }

    /**
     * Get params for the backend route
     *
     * @param array $params Additional parameters
     *
     * @return array
     */
    public function getRouteBackendParams($params = array())
    {
        $defaults = array(
            'id' => $this->id ? $this->id : 0
        );

        $params = array_merge($defaults, $params);

        return $params;
    }

    /**
     * Is Deletable
     *
     * @return bool
     */
    public function isDeletable()
    {
        return !in_array($this->getRole(), array('ROLE_DEVELOPER', 'ROLE_BACKEND_ADMIN', 'ROLE_ADMIN'));
    }

    /**
     * Not Deletable
     *
     * @return bool
     *
     * @TODO Remove this and refactor the getDeleteRestrictions functionnality
     */
    public function notDeletable()
    {
        return !$this->isDeletable();
    }

    /**
     * List of methods to check before allowing deletion
     *
     * @return array
     */
    public function getDeleteRestrictions()
    {
        return array('notDeletable');
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->role
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->role
        ) = unserialize($serialized);
    }

}
