<?php

namespace Flexy\SystemBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Flexy\SystemBundle\Generator\Tools\EntityGenerator as BaseEntityGenerator;

class EntityGenerator extends BaseEntityGenerator
{
    /**
     * Generate a PHP5 Doctrine 2 entity class from the given ClassMetadataInfo instance
     *
     * @param ClassMetadataInfo     $metadata
     * @param Bundle                $bundle
     * @param string                $entity
     * @param array                 $fields
     * @param bool                  $hasI18n
     * @param bool                  $isTimestampable
     * @param bool                  $isSluggable
     *
     * @return string $code
     */
    public function generateEntityClass(ClassMetadataInfo $metadata, $bundle = null, $entity = null, $fields = array(), $hasI18n = false, $isTimestampable = false, $isSluggable = false)
    {
        $target = sprintf('%s/Entity/%s.php', $bundle->getPath(), $entity);

        $namespace = $this->generateEntityNamespace($metadata);
        $code = str_replace('<spaces>', $this->spaces, $this->generateEntityBody($metadata));

        $bundleName = explode('\Entity', $metadata->name);
        $routePrefix = strtolower(str_replace('\\', '_', str_replace('Bundle', '', $bundleName[0]))) . '_backend';
        $routeName = $routePrefix . strtolower(str_replace('\\', '_', $bundleName[1]));

        // Track all the translation fields and check if it contains the fieldName 'name'
        // and check if the name field is i18n
        $containNameField = false;
        $nameIsI18n = false;

        foreach ($fields as $field) {
            if ($field['fieldName'] == 'name') {
                $containNameField = true;
                if (substr(strtolower($field['i18n']), 0, 1) == 'y') {
                    $nameIsI18n = true;
                }
            }
        }

        if ($containNameField) {
            $functionName = '$this->getName()';
        } else {
            $functionName = '$this->getEntityName()';
        }

        $this->renderFile('entity/Entity.php.twig', $target, array(
            'namespace' => $namespace,
            'route' => $routeName,
            'entity' => $entity,
            'entity_var' => $this->getEntityVar($entity),
            'code' => $code,
            'name_function' => $functionName,
            'is_timestampable' => $isTimestampable,
            'is_sluggable' => $isSluggable,
            'sluggable_name' => !$nameIsI18n && $isSluggable,
            'has_i18n' => $hasI18n
        ));
    }

    /**
     * Return the camelcase entity var name
     *
     * @param mixed $entity
     *
     * @return string
     */
    protected function getEntityVar($entity)
    {
        return lcfirst(Container::camelize($entity));
    }
}
