<?php

namespace Unifik\SystemBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Unifik\SystemBundle\Lib\BaseEntity;
use Unifik\DoctrineBehaviorsBundle\Model as UnifikORMBehaviors;

/**
 * Member
 */
class Member extends BaseEntity implements AdvancedUserInterface, \Serializable
{
    use UnifikORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string $firstname
     */
    private $firstname;

    /**
     * @var string $lastname
     */
    private $lastname;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $address
     */
    private $address;

    /**
     * @var string $city
     */
    private $city;

    /**
     * @var string $postalCode
     */
    private $postalCode;

    /**
     * @var string $telephone
     */
    private $telephone;

    /**
     * @var string $salt
     */
    private $salt;

    /**
     * @var boolean $emailConfirmed
     */
    private $emailConfirmed;

    /**
     * @var boolean $active
     */
    private $active;

    /**
     * @var Datetime $resetAskDate
     */
    private $resetAskDate;

    /**
     * @var string $token
     */
    private $token;

    /**
     * This is the preferred locale of the user
     *
     * @var string
     */
    private $locale;

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
        if ($this->id) {
            return $this->firstname . ' ' . $this->lastname;
        }

        return 'New member';
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
        return 'unifik_system_backend_member_' . $suffix;
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
            'id' => $this->id ? $this->id : 0,
        );

        $params = array_merge($defaults, $params);

        return $params;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set address
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set emailConfirmed
     *
     * @param boolean $emailConfirmed
     */
    public function setEmailConfirmed($emailConfirmed)
    {
        $this->emailConfirmed = $emailConfirmed;
    }

    /**
     * Get emailConfirmed
     *
     * @return boolean
     */
    public function getEmailConfirmed()
    {
        return $this->emailConfirmed;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param \Datetime $resetAskDate
     */
    public function setResetAskDate($resetAskDate)
    {
        $this->resetAskDate = $resetAskDate;
    }

    /**
     * @return \Datetime
     */
    public function getResetAskDate()
    {
        return $this->resetAskDate;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function generateToken() {
        $this->token = md5(uniqid().$this->getSalt());
        return $this->token;
    }

    public function removeToken() {
        $this->token = null;
        $this->resetAskDate = null;
    }

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->active;
    }

    /**
     * The unique identifier used by the security component to log a user
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * The main salt used for this user
     *
     * @return string
     */
    public function getSalt()
    {
        $this->salt = 'member_' . $this->id . '_sea_salt';
        return $this->salt;
    }

    /**
     * Default roles associated to a member
     *
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_MEMBER');
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
//        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
//        return $this->locale;
        return 'fr';
    }

    /**
     * Meant for erasing sensitive data before persisting a token
     */
    public function eraseCredentials()
    {
        // nothing to do
    }

    public function getResetHash() {
        return md5($this->resetAskDate->format('Y-m-d H:i:s').$this->password.$this->getSalt());
    }

    /**
     * @param  UserInterface $user
     * @return bool
     */
    public function equals(UserInterface $user)
    {
        return $this->email == $user->getUsername();
    }

    /**
     * Serialize the member
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            $this->getSalt(),
            $this->active
        ));
    }

    /**
     * Unserialize the member
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            $this->active
            ) = unserialize($serialized);
    }
}
