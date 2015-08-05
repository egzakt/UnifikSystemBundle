<?php

namespace Unifik\SystemBundle\Entity;

use Unifik\DoctrineBehaviorsBundle\Model as UnifikORMBehaviors;

/**
 * AppTranslation
 */
class AppTranslation
{
    use UnifikORMBehaviors\Translatable\Translation;
    use UnifikORMBehaviors\Sluggable\Sluggable;
    use UnifikORMBehaviors\Metadatable\Metadatable;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var boolean $active
     */
    private $active;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Get Sluggable Fields
     *
     * @return array
     */
    public function getSluggableFields()
    {
        return array('name');
    }

}
