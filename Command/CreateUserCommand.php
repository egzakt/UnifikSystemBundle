<?php

namespace Unifik\SystemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Unifik\SystemBundle\Entity\User;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

/**
 * A console command for installing unifik.
 *
 * This class is inspired from Sylius's install command.
 */
class CreateUserCommand extends ContainerAwareCommand
{
    public static $roles = array(
        'Developer' => 'ROLE_DEVELOPER',
        'Admin' => 'ROLE_BACKEND_ADMIN',
    );

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('unifik:create:user')
            ->setDescription('Creates a user for the Unifik CMS')
            ->setHelp(<<<EOF
The <info>unifik:install</info> command install a unifik application.
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
        $output->writeln('<info>Unifik user creation</info>');
        $output->writeln('');

        $dialog = $this->getDialogHelper();

        $user = new User();
        $user->setUsername($dialog->ask($output, '<question>Username:</question> '));
        $user->setFirstname($dialog->ask($output, '<question>Firstname:</question> '));
        $user->setLastname($dialog->ask($output, '<question>Lastname:</question> '));
        $user->setEmail($dialog->ask($output, '<question>Email:</question> '));
        $user->setPassword($dialog->ask($output, '<question>Password:</question> '));
        $user->setActive(true);

        // password hashing
        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        $user->setPassword($encodedPassword);

        // role
        $roleRepo = $this->getContainer()->get('doctrine')->getRepository('UnifikSystemBundle:Role');
        $roleRepo->setContainer($this->getContainer());
        $roleRepo->setCurrentAppName('backend');
        $role = false;
        $i = 0;
        while (!$role && $i++ < 5) {
            $role = $dialog->ask($output, $dialog->getQuestion('Role (' . implode(', ', array_keys(self::$roles)) . ', or the role value)', 'Developer'), 'Developer', null);
            if (array_key_exists($role, self::$roles)) {
                $role = self::$roles[$role];
            }
            $role = $roleRepo->findOneByRole($role);
            if (!$role) {
                $output->writeln('<error>This role could not be found in the database.</error>');
            }
        }
        if (!$role) {
            $output->writeln('<error>No suitable role was found after 5 tries. Aborting.</error>');
            return 1;
        }
        $user->addRole($role);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();

        return $this->success($input, $output);
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
        $output->writeln('The user has been successfully created.');

        return 0;
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}

