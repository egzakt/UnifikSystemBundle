<?php

namespace Egzakt\SystemBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;

/**
 * Egzakt Backend Doctrine Entity Generator
 */
class DoctrineEntityGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\DoctrineEntityGenerator
{

    private $filesystem;
    private $registry;

    /**
     * __construct
     *
     * @param Filesystem        $filesystem
     * @param RegistryInterface $registry
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
    }

    public function generate(BundleInterface $bundle, $entity, $format, array $fields, $withRepository)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
            array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
            $config->getEntityNamespaces()
        ));

        // Rebuild fields based on the i18n attribute
        $entityFields = array();
        $entityTranslationFields = array();
        foreach ($fields as $field) {
            if (substr(strtolower($field['i18n']), 0, 1) == 'y') { // Simulate all Yes combinations
                array_push($entityTranslationFields, $field);
                unset($entityTranslationFields['i18n']);
            } else {
                array_push($entityFields, $field);
                unset($entityFields['i18n']);
            }
        }
        $hasI18n = count($entityTranslationFields) > 0 ? true : false;

        $this->generateEntity($bundle, $entity, $format, $entityFields, $fields, $withRepository, $hasI18n);
        if ($hasI18n) {
            $this->generateEntityTranslation($bundle, $entity, $format, $entityTranslationFields, $fields, $withRepository);
        }
    }

    public function generateEntity(BundleInterface $bundle, $entity, $format, array $entityFields, array $fields, $withRepository, $hasI18n)
    {
        $entityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$entity;
        $entityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass);
        if ($withRepository) {
            $class->customRepositoryClassName = $entityClass.'Repository';
        }
        $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);

        if ($hasI18n) {
            $class->addInheritedAssociationMapping(
                array(
                    'fieldName' => 'translations',
                    'type' => 4,
                    'targetEntity' => $bundle->getNamespace() . '\Entity\\' . $entity . 'Translation',
                    'mappedBy' => 'translatable',
                    'inversedBy' => null,
                    'fetch' => 'EAGER',
                    'orphanRemoval' => false,
                    'isCascadeRemove' => false,
                    'isCascadePersist' => true,
                    'isCascadeRefresh' => false,
                    'isCascadeMerge' => false,
                    'isCascadeDetach' => false
                )
            );
        }

        foreach ($entityFields as $field) {
            $class->mapField($field);
        }

        $entityGenerator = $this->getEntityGenerator();
        if ('annotation' === $format) {
            $entityGenerator->setGenerateAnnotations(true);
            $entityCode = $entityGenerator->generateEntityClass($class, $fields);
            $mappingPath = $mappingCode = false;
        } else {
            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $entity).'.orm.'.$format;

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
            }

            $mappingCode = $exporter->exportClassMetadata($class);
            $entityGenerator->setGenerateAnnotations(false);
            $entityCode = $entityGenerator->generateEntityClass($class, $fields);
        }

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
        }

        if ($withRepository) {
            $path = $bundle->getPath().str_repeat('/..', substr_count(get_class($bundle), '\\'));
            $this->getRepositoryGenerator()->writeEntityRepositoryClass($class->customRepositoryClassName, $path);
        }
    }

    public function generateEntityTranslation(BundleInterface $bundle, $entity, $format, array $entityFields, array $fields, $withRepository)
    {
        $entityTranslation = $entity . 'Translation';

        $entityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$entityTranslation;
        $entityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $entityTranslation).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass);
        if ($withRepository) {
            $class->customRepositoryClassName = $entityClass.'Repository';
        }
        $class->mapField(array('fieldName' => 'id', 'type' => 'integer', 'id' => true));
        $class->mapField(array('fieldName' => 'locale', 'type' => 'string', 'length' => 5));
        $class->setIdGeneratorType(ClassMetadataInfo::GENERATOR_TYPE_AUTO);
        $class->addInheritedAssociationMapping(
            array(
                'fieldName' => 'translatable',
                'type' => 2,
                'targetEntity' => $bundle->getNamespace() . '\Entity\\' . $entity,
                'inversedBy' => 'translations',
                'joinColumns' => array(
                    array(
                        'name' => 'translatableId',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'cascade'
                    )
                ),
                'mappedBy' => null,
                'orphanRemoval' => false,
                'isCascadeRemove' => false,
                'isCascadePersist' => false,
                'isCascadeRefresh' => false,
                'isCascadeMerge' => false,
                'isCascadeDetach' => false
            )
        );

        foreach ($entityFields as $field) {
            $class->mapField($field);
        }

        $entityGenerator = $this->getEntityTranslationGenerator();
        if ('annotation' === $format) {
            $entityGenerator->setGenerateAnnotations(true);
            $entityCode = $entityGenerator->generateEntityClass($class, $fields);
            $mappingPath = $mappingCode = false;
        } else {
            $cme = new ClassMetadataExporter();
            $exporter = $cme->getExporter('yml' == $format ? 'yaml' : $format);
            $mappingPath = $bundle->getPath().'/Resources/config/doctrine/'.str_replace('\\', '.', $entityTranslation).'.orm.'.$format;

            if (file_exists($mappingPath)) {
                throw new \RuntimeException(sprintf('Cannot generate entity when mapping "%s" already exists.', $mappingPath));
            }

            $mappingCode = $exporter->exportClassMetadata($class);
            $entityGenerator->setGenerateAnnotations(false);
            $entityCode = $entityGenerator->generateEntityClass($class, $fields);
        }

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);

        if ($mappingPath) {
            $this->filesystem->mkdir(dirname($mappingPath));
            file_put_contents($mappingPath, $mappingCode);
        }

        if ($withRepository) {
            $path = $bundle->getPath().str_repeat('/..', substr_count(get_class($bundle), '\\'));
            $this->getRepositoryGenerator()->writeEntityRepositoryClass($class->customRepositoryClassName, $path);
        }
    }

    public function isReservedKeyword($keyword)
    {
        return $this->registry->getConnection()->getDatabasePlatform()->getReservedKeywordsList()->isKeyword($keyword);
    }

    /**
     * Get Entity Generator
     *
     * @return \Egzakt\SystemBundle\Generator\EntityGenerator
     */
    protected function getEntityGenerator()
    {
        $entityGenerator = new \Egzakt\SystemBundle\Generator\EntityGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

    /**
     * Get EntityTranslation Generator
     *
     * @return \Egzakt\SystemBundle\Generator\EntityTranslationGenerator
     */
    protected function getEntityTranslationGenerator()
    {
        $entityGenerator = new \Egzakt\SystemBundle\Generator\EntityTranslationGenerator();
        $entityGenerator->setGenerateAnnotations(false);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }

    /**
     * Get Repository Generator
     *
     * @return \Egzakt\SystemBundle\Generator\EntityRepositoryGenerator
     */
    protected function getRepositoryGenerator()
    {
        return new \Egzakt\SystemBundle\Generator\EntityRepositoryGenerator();
    }
}