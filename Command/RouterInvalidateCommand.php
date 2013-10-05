<?php

namespace Flexy\SystemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

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
            ->setName('flexy:router:invalidate')
            ->setDescription('Invalidate the router cache')
            ->setHelp(<<<EOF
The <info>flexy:router:invalidate</info> invalidate (clear) the router cache

<info>php app/console flexy:router:invalidate --env=dev</info>
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

        $this->getContainer()->get('flexy_system.router_cache')->invalidate();
    }

}
