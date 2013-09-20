<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Egzakt\SystemBundle\Entity\User;
use Egzakt\SystemBundle\Lib\BaseEntity;

use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

/**
 * Role
 */
class Role extends BaseEntity implements RoleInterface, \Serializable
{
    use EgzaktORMBehaviors\Translatable\Translatable;
    use EgzaktORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string $role
     */
    private $role;

    /**
     * @var ArrayCollection
     */
    private $users;

    /**
     * @var ArrayCollection
     */
    private $sections;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New role';
        }

        if ($name = $this->getName()) {
            return $name;
        }

        // No translation found in the current locale
        return '';
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->sections = new ArrayCollection();
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
