<?php

namespace Egzakt\SystemBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineEntityCommand as BaseGenerateEntityCommand;
use Egzakt\SystemBundle\Generator\DoctrineEntityGenerator as EgzaktDoctrineEntityGenerator;

/**
 * Generate Entity Command
 *
 * @throws \InvalidArgumentException
 */
class GenerateEntityCommand extends BaseGenerateEntityCommand
{
    /**
     * @var \Egzakt\SystemBundle\Generator\DoctrineEntityGenerator
     */
    private $generator;

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('egzakt:generate:entity')
            ->setDescription('Generate a new Egzakt entity inside a bundle')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('fields', null, InputOption::VALUE_REQUIRED, 'The fields to create with the new entity')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)', 'yml')
            ->addOption('with-repository', null, InputOption::VALUE_REQUIRED, 'Whether to generate the entity repository or not', 'yes');
    }

    /**
     * Get Generator
     *
     * @return \Egzakt\SystemBundle\Generator\DoctrineEntityGenerator
     */
    public function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $this->generator = new EgzaktDoctrineEntityGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('doctrine'));
        }

        return $this->generator;
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        $skeletonDirs[] = __DIR__.'/../Resources/skeleton';
        $skeletonDirs[] = __DIR__.'/../Resources';

        return $skeletonDirs;
    }
}