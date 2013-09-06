<?php

namespace Egzakt\SystemBundle\Generator;

use \Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * EntityTranslation Generator
 */
class EntityTranslationGenerator extends \Doctrine\ORM\Tools\EntityGenerator
{

    /**
     * Generate EntityTranslation Class
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata Metadata Info
     *
     * @return string
     */
    public function generateEntityClass(ClassMetadataInfo $metadata, array $fields = array())
    {
        parent::setFieldVisibility('protected');

        $code = parent::generateEntityClass($metadata);

        $shortClassName = explode('\\', $metadata->name);
        $shortClassName = 'class ' . end($shortClassName);

        // Adding custom extends
        $code = str_replace($shortClassName, $shortClassName . ' extends BaseTranslationEntity', $code);

        // Adding custom use statement
        $useStatements = 'use Egzakt\SystemBundle\Lib\BaseTranslationEntity;';
        $startString = 'use Doctrine\ORM\Mapping as ORM;';
        $code = str_replace($startString, $startString . "\n\n" . $useStatements, $code);

        return $code;
    }
}
