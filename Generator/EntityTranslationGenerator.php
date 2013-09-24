<?php

namespace Egzakt\SystemBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Egzakt\SystemBundle\Tools\EntityGenerator as BaseEntityGenerator;

/**
 * EntityTranslation Generator
 */
class EntityTranslationGenerator extends BaseEntityGenerator
{

    /**
     * Generate a PHP5 Doctrine 2 entity class from the given ClassMetadataInfo instance
     *
     * @param ClassMetadataInfo     $metadata
     * @param Bundle                $bundle
     * @param string                $entity
     * @param array                 $fields
     * @param bool                  $isSluggable
     *
     * @return string $code
     */
    public function generateEntityClass(ClassMetadataInfo $metadata, $bundle = null, $entity = null, $fields = array(), $isSluggable = false)
    {
        $target = sprintf('%s/Entity/%s.php', $bundle->getPath(), $entity);

        $parts = explode('\\', $entity);

        $entityNamespace = implode('\\', $parts);
        $namespace = $this->getNamespace($metadata);
        $code = str_replace('<spaces>', $this->spaces, $this->generateEntityBody($metadata));

        $bundleName = explode('\Entity', $metadata->name);
        $routePrefix = strtolower(str_replace('\\', '_', str_replace('Bundle', '', $bundleName[0]))) . '_backend';
        $routeName = $routePrefix . strtolower(str_replace('\\', '_', $bundleName[1]));

        // Track all the translation fields and check if it contains the fieldName 'name'
        // or a slug
        $containNameField = false;
        foreach ($fields as $field) {
            if ($field['fieldName'] == 'name' && (substr(strtolower($field['i18n']), 0, 1) == 'y')) {
                $containNameField = true;
            }
        }

        $this->renderFile('entity/EntityTranslation.php.twig', $target, array(
            'entity_namespace' => $entityNamespace,
            'namespace' => $namespace,
            'route' => $routeName,
            'entity' => $entity,
            'code' => $code,
            'is_sluggable' => $isSluggable,
            'sluggable_name' => $isSluggable && $containNameField
        ));
    }
}
