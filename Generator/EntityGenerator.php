<?php

namespace Egzakt\SystemBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Egzakt\SystemBundle\Tools\EntityGenerator as BaseEntityGenerator;

class EntityGenerator extends BaseEntityGenerator
{

    /**
     * Generate a PHP5 Doctrine 2 entity class from the given ClassMetadataInfo instance
     *
     * @param ClassMetadataInfo     $metadata
     * @param Bundle                $bundle
     * @param string                $entity
     * @param array                 $fields
     *
     * @return string $code
     */
    public function generateEntityClass(ClassMetadataInfo $metadata, $bundle = null, $entity = null, $fields = array())
    {
        $target = sprintf('%s/Entity/%s.php', $bundle->getPath(), $entity);

        $this->setFieldVisibility('protected');

        $parts = explode('\\', $entity);

        $entityNamespace = implode('\\', $parts);
        $namespace = $this->getNamespace($metadata);
        $code = str_replace('<spaces>', $this->spaces, $this->generateEntityBody($metadata));

        $bundleName = explode('\Entity', $metadata->name);
        $routePrefix = strtolower(str_replace('\\', '_', str_replace('Bundle', '', $bundleName[0]))) . '_backend';
        $routeName = $routePrefix . strtolower(str_replace('\\', '_', $bundleName[1]));

        // Track all the translation fields and check if it contains the fieldName 'name'
        $containNameField = false;
        foreach ($fields as $field) {
            if (substr(strtolower($field['i18n']), 0, 1) == 'y') {
                $field['fieldName'] == 'name' ? $containNameField = true : null;
            }
        }

        if ($containNameField) {
            $functionName = '$this->translate()->getName()';
        } else {
            $functionName = '$this->getEntityName()';
        }

        $this->renderFile('entity/Entity.php.twig', $target, array(
            'entity_namespace' => $entityNamespace,
            'namespace' => $namespace,
            'route' => $routeName,
            'entity' => $entity,
            'code' => $code,
            'name_function' => $functionName
        ));
    }

}
