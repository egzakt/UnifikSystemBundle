<?php

namespace Egzakt\SystemBundle\Generator;

/**
 * Entity Repository Generator
 */
class EntityRepositoryGenerator extends \Doctrine\ORM\Tools\EntityRepositoryGenerator
{
    /**
     * Generate entity repository class
     *
     * @param string $fullClassName
     *
     * @return string
     */
    public function generateEntityRepositoryClass($fullClassName)
    {
        $code = parent::generateEntityRepositoryClass($fullClassName);

        // Extend our custom entity repository
        $code = str_replace('use Doctrine\ORM\EntityRepository;', 'use Egzakt\SystemBundle\Lib\BaseEntityRepository;', $code);
        $code = str_replace('extends EntityRepository', 'extends BaseEntityRepository', $code);

        return $code;
    }
}