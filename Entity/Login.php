<?php

namespace Egzakt\SystemBundle\Entity;

use Egzakt\SystemBundle\Lib\BaseEntity;

/**
 * Login
 */
class Login extends BaseEntity
{
    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $ip
     */
    private $ip;

    /**
     * @var boolean $success
     */
    private $success;

    /**
     * @var \DateTime $createdAt
     */
    private $createdAt;

    /**
     * @return string
     */
    public function __toString()
    {
        if (false == $this->id) {
            return 'New login';
        }

        if ($this->username) {
            return $this->username . ' - ' . $this->ip;
        }

        return '';
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set success
     *
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * Get success
     *
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
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

}
