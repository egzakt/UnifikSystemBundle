<?php

namespace Egzakt\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Egzakt\SystemBundle\Entity\User;
use Egzakt\SystemBundle\Entity\RoleTranslation;
use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Role
 */
class Role extends BaseEntity
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $roleName
     */
    protected $roleName;

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
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New role';
        }

        if ($name = $this->roleName) {
            return $name;
        }

        // No translation found in the current locale
        return '';
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

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
     * Set roleName
     *
     * @param string $roleName
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

    /**
     * Get roleName
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->roleName;
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
     * @param \Egzakt\SystemBundle\Entity\RoleTranslation $translations
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
}