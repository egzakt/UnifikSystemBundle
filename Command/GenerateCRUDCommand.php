<?php

namespace Egzakt\SystemBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

use Egzakt\SystemBundle\Command\Generator;
use Egzakt\SystemBundle\Generator\DoctrineFormGenerator;

/**
 * Generate CRUD Command
 */
class GenerateCRUDCommand extends GenerateDoctrineCrudCommand
{
    /* @var Generator */
    private $generator;

    /* @var DoctrineFormGenerator */
    private $formGenerator;

    /* @var string */
    private $bundle;

    /* @var string */
    private $application;

    /**
     * Configure
     */
    protected function configure()
    {
        $setDefinitionOptions = array(
            new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)'),
            new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
        );
        $this->setName('egzakt:generate:crud')
            ->setDescription('Generates a CRUD based on an Egzakt entity')
            ->setDefinition($setDefinitionOptions);

    }

    /**
     * Execute
     *
     * @param InputInterface $input      The Input Interface
     * @param OutputInterface $output     The Output Interface
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');
                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $dialog->writeSection($output, 'CRUD generation');

        $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundle) . '\\' . $entity;
        $metadata = $this->getEntityMetadata($entityClass);

        // Custom route_prefix
        $prefix = $this->getRoutePrefix($input, str_replace('Bundle\Entity', '\\' . $this->application, $entityClass));

        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        // Generate the controller
        $generator = $this->getGenerator();
        $generator->generate($bundle, $entity, $metadata[0], 'yml', $prefix, true, true, $this->application);
        $output->writeln('Generating the Controller code: <info>OK</info>');

        $errors = array();

        // Check if we create a Translation Form or not
        $translation = array();
        if (isset($metadata[0]->associationMappings['translations'])) {

            $translationEntityClass = $metadata[0]->associationMappings['translations']['targetEntity'];
            $entityTranslation = str_replace('\\', '', explode('\Entity', $translationEntityClass))[1];
            $translationMetadata = $this->getEntityMetadata($translationEntityClass);

            $translation['entity'] = $entityTranslation;
            $translation['entityClass'] = $translationEntityClass;
            $translation['metadata'] = $translationMetadata[0];
        }

        // form
        $this->generateForm($bundle, $entity, $metadata, $translation);
        $output->writeln('Generating the Form code: <info>OK</info>');

        $dialog->writeGeneratorSummary($output, $errors);
    }

    /**
     * Interact
     *
     * @param InputInterface $input      The Input Interface
     * @param OutputInterface $output     The Output Interface
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Egzakt CMS CRUD generator');

        // namespace
        $output->writeln(
            array(
                '',
                'This command helps you generate CRUD controllers and templates.',
                '',
                'First, you need to give the entity for which you want to generate a CRUD.',
                '',
                'You must use the shortcut notation like <comment>EgzaktBlogBundle:Post</comment>.',
                '',
            )
        );

        $bundleNames = array_keys($this->getContainer()->get('kernel')->getBundles());

        while (true) {
            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'), $bundleNames);

            list($bundle, $entity) = $this->parseShortcutNotation($entity);

            try {
                $b = $this->getContainer()->get('kernel')->getBundle($bundle);

                if (file_exists($b->getPath() . '/Entity/' . str_replace('\\', '/', $entity) . '.php')) {
                    break;
                }

                $output->writeln(sprintf('<bg=red>Entity "%s:%s" does not exists</>.', $bundle, $entity));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }
        }
        $input->setOption('entity', $bundle . ':' . $entity);

        // Application
        $this->application = $dialog->ask($output, $dialog->getQuestion('Application (Backend, Frontend, etc.)', 'Backend'), 'Backend', null);

        // summary
        $output->writeln(
            array(
                '',
                $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
                '',
                sprintf("You are going to generate a CRUD controller for \"<info>%s:%s</info>\"", $bundle, $entity),
                sprintf("using the \"<info>%s</info>\" format.", 'yml'),
                sprintf("with the route prefix \"<info>%s</info>\" ", $bundle),
                sprintf("in the \"<info>%s</info>\" application", $this->application),
                '',
            )
        );
    }

    /**
     * Get Generator
     *
     * @return Generator
     */
    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            $bundleDir = __DIR__ . '/../Resources/skeleton/crud';
            $this->generator = new Generator($this->getContainer()->get('filesystem'), $bundleDir, $bundle);
            $this->generator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->generator;
    }


    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        $skeletonDirs[] = __DIR__ . '/../Resources/skeleton';
        $skeletonDirs[] = __DIR__ . '/../Resources';

        return $skeletonDirs;
    }

    /**
     * Get Form Generator
     *
     * @return DoctrineFormGenerator
     */
    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new \Egzakt\SystemBundle\Generator\DoctrineFormGenerator(
                $this->getContainer()->get('filesystem')
            );
            $this->formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->formGenerator;
    }

    /**
     * Tries to generate forms if they don't exist yet and if we need write operations on entities.
     *
     * @param BundleInterface $bundle         The bundle in which to create the class
     * @param string $entity         The entity relative class name
     * @param ClassMetadataInfo $metadata       The entity metadata class
     * @param array $translation    array used for the translation Form
     */
    protected function generateForm($bundle, $entity, $metadata, $translation = array())
    {
        try {
            $this->getFormGenerator()->generate($bundle, $entity, $metadata[0], $this->application, $translation);
        } catch (\RuntimeException $e) {
            // form already exists
            $e->getCode();
        }
    }

}