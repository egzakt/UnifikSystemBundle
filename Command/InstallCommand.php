<?php

namespace Flexy\SystemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Flexy\SystemBundle\Entity\User;

/**
 * A console command for installing flexy.
 *
 * This class is inspired from Sylius's install command.
 */
class InstallCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('flexy:install')
            ->setDescription('Flexy installer')
            ->setHelp(<<<EOF
The <info>flexy:install</info> command install a flexy application.
EOF
            )
        ;
    }

    /**
     * Main entry point
     *
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(
            '    ________
               / ____/ /__  _  ____  __
              / /_  / / _ \| |/_/ / / /
             / __/ / /  __/>  </ /_/ /
            /_/   /_/\___/_/|_|\__, /
                              /____/
            ');
        $output->writeln('<info>Installation.</info>');
        $output->writeln('');

        $this->doDatabaseSetup($input, $output);
        $this->doFixturesLoad($input, $output);
        $this->doAssetsDump($input, $output);
        $this->doAdministrationSetup($input, $output);

        return $this->success($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function doDatabaseSetup(InputInterface $input, OutputInterface $output)
    {
        $output->write('<info>Testing database connection...</info> ');

        $dialog = $this->getHelperSet()->get('dialog');
        $silentOutput = new ConsoleOutput();
        $silentOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        if ($this->checkDatabaseConnection()) {
            $output->writeln('OK');
        } else {
            try {
                $this->checkDatabaseConnection(true);
            } catch (\PDOException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return $this->faillure($input, $output);
            }
        }

        $output->write('<info>Testing database presence...</info>   ');

        if ($this->checkDatabaseExist()) {
            $output->writeln('OK');
        } else {
            try {
                $this->checkDatabaseExist(true);
            } catch (\PDOException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
            $dbname = $this->getContainer()->get('doctrine')->getManager()->getConnection()->getParams()['dbname'];
            $output->writeln('');
            if ($dialog->askConfirmation($output, '<question>Create the "' . $dbname . '" database Y/N ?</question> ', false)) {
                $this->runCommand('doctrine:database:create', $input, $output);
            } else {
                $output->writeln('<info>Flexy installation aborded.</info>');
                return $this->faillure($input, $output);
            }
        }

        $output->write('<info>Creating database schema...</info>    ');

        if (false == $this->checkSchemaExist()) {
            $this->runCommand('doctrine:schema:create', $input, $silentOutput);
            $output->writeln('OK');
        } else {
            $output->writeln('<error>schema already exist</error>');
            try {
                $this->checkSchemaExist(true);
            } catch (\PDOException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                return $this->faillure($input, $output);
            }
            $dbname = $this->getContainer()->get('doctrine')->getManager()->getConnection()->getParams()['dbname'];
            $output->writeln('');
            if ($dialog->askConfirmation($output, '<question>Drop and recreate the "' . $dbname . '" schema Y/N ?</question> ', false)) {
                $this->runCommand('doctrine:schema:drop', new ArrayInput(array('--force' => true, '')), $silentOutput);
                $this->runCommand('doctrine:schema:create', $input, $silentOutput);
            } else {
                return $this->faillure($input, $output);
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function doFixturesLoad(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        if ($dialog->askConfirmation($output, '<question>Load data fixtures Y/N ?</question> ', false)) {
            $this->runCommand('doctrine:fixtures:load', $input, $output);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function doAssetsDump(InputInterface $input, OutputInterface $output)
    {
        $silentOutput = new ConsoleOutput();
        $silentOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $output->write('<info>Dumping assets...</info>    ');
        $this->runCommand('assetic:dump', $input, $silentOutput);
        $output->writeln('OK');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function doAdministrationSetup(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>Administration setup.</info>');
        $output->writeln('');

        $dialog = $this->getHelperSet()->get('dialog');

        $user = new User();
        $user->setUsername($dialog->ask($output, '<question>Username:</question> '));
        $user->setFirstname($dialog->ask($output, '<question>Firstname:</question> '));
        $user->setLastname($dialog->ask($output, '<question>Lastname:</question> '));
        $user->setEmail($dialog->ask($output, '<question>Email:</question> '));
        $user->setPassword($dialog->ask($output, '<question>Password:</question> '));
        $user->setSalt(uniqid());
        $user->setActive(true);

        // password hashing
        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($encodedPassword);

        // default roles
        $roleRepo = $this->getContainer()->get('doctrine')->getRepository('FlexySystemBundle:Role');
        $roleRepo->setContainer($this->getContainer());
        $roleRepo->setCurrentAppName('backend');
        $role = $roleRepo->findOneByRole('ROLE_BACKEND_ADMIN');
        $user->addRole($role);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();
    }

    /**
     * Final success response that returns an exit code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function success(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('Flexy has been successfully installed.');

        return 0;
    }

    /**
     * Final faillure response that returns an exit code
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    private function faillure(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<error>Flexy could not be installed. Fix the error above and restart the install procedure.</error>');

        return 1;
    }

    /**
     * Check if the default database schema exist and is accessible
     *
     * @param bool $throwException
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function checkSchemaExist($throwException = false)
    {
        try {
            $schemaManager = $this->getContainer()->get('doctrine')->getConnection()->getSchemaManager();
            return $schemaManager->tablesExist(array('section'));
        } catch (\Exception $e) {
            if ($throwException) {
                throw $e;
            }
        }
    }

    /**
     * Check if the default database connection is usable
     *
     * @param bool $throwException
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function checkDatabaseConnection($throwException = false)
    {
        try {
            $this->getContainer()->get('doctrine')->getManager()->getConnection()->connect();
        } catch (\Exception $e) {
            if ($throwException) {
                throw $e;
            }
            // code 1049 is ignored as this means that the connection works but the database do not exist
            if (1049 !== $e->getCode()) {
                return false;
            }
        }

        return true;
    }


    /**
     * Check if the default database is usable
     *
     * @param bool $throwException
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function checkDatabaseExist($throwException = false)
    {
        try {
            $this->getContainer()->get('doctrine')->getManager()->getConnection()->connect();
        } catch (\Exception $e) {
            if ($throwException) {
                throw $e;
            }
            return false;
        }

        return true;
    }

    /**
     * Run another symfony command
     *
     * @param $command
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return $this
     */
    private function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $this
            ->getApplication()
            ->find($command)
            ->run($input, $output)
        ;

        return $this;
    }
}
