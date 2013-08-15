<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Egzakt\SystemBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator as BaseDoctrineFormGenerator;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class DoctrineFormGenerator extends BaseDoctrineFormGenerator
{
    /* @var $filesystem Filesystem */
    private $filesystem;

    /* @var $className string */
    private $className;

    /* @var $classPath string */
    private $classPath;

    /**
     * __construct
     *
     * @param Filesystem    $filesystem     The File System
     * @param string        $skeletonDir    The directory of the Skeleton
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Returns the Class Name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Returns the Class Path
     *
     * @return string
     */
    public function getClassPath()
    {
        return $this->classPath;
    }

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface       $bundle         The bundle in which to create the class
     * @param string                $entity         The entity relative class name
     * @param ClassMetadataInfo     $metadata       The entity metadata class
     * @param string                $application    The application context
     * @param array                 $translation    Array used for the translation Form
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $application = '', $translation = array())
    {
        // Recursive to create the Translation Form
        if ($translation) {
            $this->generate($bundle, $translation['entity'], $translation['metadata'], $application, false);
        }

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $this->className = $entityClass . 'Type';
        $dirPath = $bundle->getPath() . '/Form';
        $this->classPath = $dirPath . '/' . $application . '/' . str_replace('\\', '/', $entity) . 'Type.php';

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        // Reordering boolean fields at the beginning
        $fields = array();
        $fieldsBoolean = array();

        foreach ($metadata->fieldMappings as $key => $field) {

            if ($field['type'] == 'boolean') {
                $fieldsBoolean[$key] = $field;
            } else {
                $fields[$key] = $field;
            }
        }

        $fields = $fieldsBoolean + $fields;

        $this->renderFile('form/FormType.php.twig', $this->classPath, array(
            'fields' => $fields,
            'namespace' => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'form_class' => $this->className,
            'form_type_name' => strtolower($this->className),
            'application' => $application,
            'entity' => $entity,
            'translation' => $translation
        ));
    }

}
