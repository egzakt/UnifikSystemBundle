<?php

namespace Unifik\SystemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command for invalidating (clearing) the router cache
 */
class RouterInvalidateCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('unifik:router:invalidate')
            ->setDescription('Invalidate the router cache')
            ->setHelp(<<<EOF
The <info>unifik:router:invalidate</info> invalidate (clear) the router cache

<info>php app/console unifik:router:invalidate --env=dev</info>
EOF
            )
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');
        $output->writeln(sprintf('Invalidating routes for the <info>%s</info> environment', $kernel->getEnvironment()));

        $this->getContainer()->get('unifik_system.router_cache')->invalidate();
    }

}
