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
    /**
     * __construct
     *
     * @param Filesystem        $filesystem
     * @param RegistryInterface $registry
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry)
    {
        parent::__construct($filesystem, $registry);
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
     * Get Repository Generator
     *
     * @return \Egzakt\SystemBundle\Generator\EntityRepositoryGenerator
     */
    protected function getRepositoryGenerator()
    {
        return new \Egzakt\SystemBundle\Generator\EntityRepositoryGenerator();
    }
}
