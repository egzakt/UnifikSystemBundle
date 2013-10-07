<?php

namespace Flexy\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TokenList
 */
class TokenList
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $tokens;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tokens = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add Token
     *
     * @param \Flexy\SystemBundle\Entity\Token $token
     * @return Token
     */
    public function addToken($token)
    {
        $this->tokens[] = $token;

        return $this;
    }

    /**
     * Remove Token
     *
     * @param \Flexy\SystemBundle\Entity\Token $token
     */
    public function removeToken($token)
    {
        $this->tokens->removeElement($token);
    }

    /**
     * Get tokens
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Set the array of Token
     *
     * @param array $tokens An array of Token
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    }
}