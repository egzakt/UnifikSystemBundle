<?php

namespace Egzakt\SystemBundle\Entity;

use Symfony\Component\Validator\ExecutionContextInterface;

use Egzakt\DoctrineBehaviorsBundle\Model as EgzaktORMBehaviors;

/**
 * TextTranslation
 */
class TextTranslation
{
    use EgzaktORMBehaviors\Translatable\Translation;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $anchor
     */
    private $anchor;

    /**
     * @var boolean $active
     */
    private $active;

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
     * Set text
     *
     * @param text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set anchor
     *
     * @param string $anchor
     */
    public function setAnchor($anchor)
    {
        $this->anchor = $anchor;
    }

    /**
     * Get anchor
     *
     * @return string
     */
    public function getAnchor()
    {
        return $this->anchor;
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
     * Validate the sub-fields of a collapsable text
     *
     * @param ExecutionContextInterface $context The Execution Context
     */
    public function isCollapsableValid(ExecutionContextInterface $context)
    {
        if ($this->translatable->getCollapsable() && false == $this->getName()) {
            $context->addViolationAt('name', 'A collapsable text must have a name');
        }
    }

}
