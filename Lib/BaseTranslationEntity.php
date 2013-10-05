<?php

namespace Flexy\SystemBundle\Lib;

/**
 * Flexy Backend Base for Translation Entities
 */
abstract class BaseTranslationEntity
{

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->getId()) {
            if (method_exists($this, 'getName')) {
                return $this->getName() ? $this->getName() : 'Untitled';
            }

            return $this->getEntityName();
        }

        if (!$this->translatable->getId()) {
            return 'New ' . $this->translatable->getEntityName();
        }

        return '';
    }

    /**
     * Returns the entity name without its path
     *
     * @return string
     */
    public function getEntityName()
    {
        $className = get_class($this);
        $classNameTokens = explode('\\', $className);

        return array_pop($classNameTokens);
    }
}
