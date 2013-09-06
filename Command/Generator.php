<?php

namespace Egzakt\SystemBundle\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Egzakt Backend Crud Generator
 *
 * @throws \RuntimeException
 */
class Generator extends DoctrineCrudGenerator
{

    /* @var Filesystem */
    protected $filesystem;

    /* @var string */
    protected $skeletonDir;

    /* @var string */
    protected $routePrefix;

    /* @var Bundle */
    protected $bundle;

    /* @var string */
    protected $bundleName;

    /* @var string */
    protected $entity;

    /* @var ClassMetadataInfo */
    protected $metadata;

    /* @var string */
    protected $format;

    /* @var array */
    protected $actions;

    /* @var string */
    protected $application;

    /** @var boolean */
    protected $withjqgrid;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem  A Filesystem instance
     * @param string     $skeletonDir Path to the skeleton directory
     * @param string     $bundleName  The Name of the Bundle
     */
    public function __construct(Filesystem $filesystem, $skeletonDir, $bundleName)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->bundleName = $bundleName;
    }

    /**
     * Generate the CRUD controller.
     *
     * @param BundleInterface   $bundle           A bundle object
     * @param string            $entity           The entity relative class name
     * @param ClassMetadataInfo $metadata         The entity class metadata
     * @param string            $format           The configuration format (xml, yaml, annotation)
     * @param string            $routePrefix      The route name prefix
     * @param array             $needWriteActions Wether or not to generate write actions
     * @param string            $forceOverwrite   Overwrite the files or not
     * @param string            $application      The current application context
     * @param boolean           $withjqgrid       Check if we use jqgrid or not
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite, $application = '', $withjqgrid = false)
    {
        $this->routePrefix = $routePrefix;
        $this->actions = array('list', 'edit', 'delete');

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The CRUD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }

        if (in_array('ordering', $metadata->fieldNames)) {
            array_push($this->actions, 'order');
        }

        $this->entity = $entity;
        $this->bundle = $bundle;
        $this->metadata = $metadata;
        $this->setFormat($format);
        $this->application = $application;
        $this->withjqgrid = $withjqgrid;

        $this->generateControllerClass($forceOverwrite);
        $this->generateNavigationClass();

        $dir = sprintf('%s/Resources/views/%s/%s', $this->bundle->getPath(), $this->application, str_replace('\\', '/', $this->entity));

        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }

        $this->generateLayoutView($dir);

        $this->generateNavigationView($dir);

        $this->generateIndexView($dir);

        if (in_array('edit', $this->actions)) {
            $this->generateEditView($dir);
        }

        $this->generateTestClass();
        $this->generateConfiguration();

    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Generates the routing configuration.
     */
    protected function generateConfiguration()
    {
        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/routing_%s.%s',
            $this->bundle->getPath(),
            strtolower($this->application),
            $this->format
        );

        $filename = 'crud/';
        $filename.= $this->withjqgrid ? 'jqgrid/' : '';
        $filename.= 'config/routing.'.$this->format.'.twig';

        $this->renderFile($filename, $target, array(
            'actions'       => $this->actions,
            'route_prefix'  => $this->routePrefix,
            'bundle'        => $this->bundle->getName(),
            'entity'        => $this->entity,
            'application'   => $this->application,
        ));
    }

    /**
     * Generates the controller class only.
     */
    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%s/%sController.php',
            $dir,
            $this->application,
            str_replace('\\', '/', $entityClass),
            $entityClass
        );

        $filename = 'crud/';
        $filename.= $this->withjqgrid ? 'jqgrid/' : '';
        $filename.= 'controller.php.twig';

        $this->renderFile($filename, $target, array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
            'application'       => $this->application,
        ));
    }

    /**
     * Generate Navigation Class
     */
    private function generateNavigationClass()
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%s/NavigationController.php',
            $dir,
            $this->application,
            str_replace('\\', '/', $entityClass)
        );

        $this->renderFile('crud/navigation.php.twig', $target, array(
            'entity'            => $this->entity,
            'route_prefix'      => $this->routePrefix,
            'bundle'            => $this->bundle->getName(),
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'application'       => $this->application,
        ));
    }

    /**
     * Generates the functional test class only.
     */
    protected function generateTestClass()
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $dir = $this->bundle->getPath() . '/Tests/Controller';
        $target = $dir . '/' . str_replace('\\', '/', $entityNamespace) . '/' . $entityClass . 'ControllerTest.php';

        $this->renderFile('crud/tests/test.php.twig', $target, array(
            'route_prefix'      => $this->routePrefix,
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'actions'           => $this->actions,
            'application'       => $this->application,
        ));
    }

    /**
     * Generate Layout View
     *
     * @param string $dir A directory
     */
    private function generateLayoutView($dir)
    {
        $this->renderFile('crud/views/layout.html.twig.twig', $dir . '/layout.html.twig', array(
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'bundle_name'       => $this->bundle->getName(),
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'application'       => $this->application,
        ));
    }

    /**
     * Generate Navigation View
     *
     * @param string $dir A directory
     */
    private function generateNavigationView($dir)
    {
        $this->renderFile('crud/views/navigation/section_module_bar.html.twig.twig', $dir . '/Navigation/section_module_bar.html.twig', array(
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'bundle_name'       => $this->bundle->getName(),
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'application'       => $this->application,
        ));
    }

    /**
     * Generates the index.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateIndexView($dir)
    {
        $filename = 'crud/';
        $filename.= $this->withjqgrid ? 'jqgrid/' : '';
        $filename.= 'views/list.html.twig.twig';

        $this->renderFile($filename, $dir . '/' . $this->entity . '/list.html.twig', array(
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'bundle_name'       => $this->bundle->getName(),
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'application'       => $this->application,
        ));
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateEditView($dir)
    {
        $this->renderFile('crud/views/edit.html.twig.twig', $dir . '/' . $this->entity . '/edit.html.twig', array(
            'route_prefix'      => $this->routePrefix,
            'bundle_name'       => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'application'       => $this->application,
        ));
    }

    /**
     * Returns an array of record actions to generate (edit, show).
     *
     * @return array
     */
    protected function getRecordActions()
    {
        return array_filter($this->actions, function ($item) {
            return in_array($item, array('show', 'edit', 'delete'));
        });
    }
}
