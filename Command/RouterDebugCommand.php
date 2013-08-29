<?php

namespace Egzakt\SystemBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Symfony\Bundle\FrameworkBundle\Command\RouterDebugCommand as BaseRouterDebugCommand;

/**
 * A console command for retrieving information about routes
 *
 * This Egzakt version display addional informations about route mapping
 */
class RouterDebugCommand extends BaseRouterDebugCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('egzakt:router:debug')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::OPTIONAL, 'A route name')
            ))
            ->setDescription('Displays current routes for an application')
            ->setHelp(<<<EOF
The <info>%command.name%</info> displays the configured routes:

  <info>php %command.full_name%</info>
EOF
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function outputRoutes(OutputInterface $output, $routes = null)
    {
        if (null === $routes) {
            $routes = $this->getContainer()->get('router')->getRouteCollection()->all();
        }

        $output->writeln($this->getHelper('formatter')->formatSection('router', 'Current routes'));

        $maxName = strlen('name');
        $maxMethod = strlen('method');
        $maxScheme = strlen('scheme');
        $maxHost = strlen('host');
        $maxMappingSource = strlen('Source');
        $maxApplication = strlen('Application');
        $maxPath = strlen('Path');

        foreach ($routes as $name => $route) {
            $method = $route->getMethods() ? implode('|', $route->getMethods()) : 'ANY';
            $scheme = $route->getSchemes() ? implode('|', $route->getSchemes()) : 'ANY';
            $host = '' !== $route->getHost() ? $route->getHost() : 'ANY';
            $maxName = max($maxName, strlen($name));
            $maxMethod = max($maxMethod, strlen($method));
            $maxScheme = max($maxScheme, strlen($scheme));
            $maxHost = max($maxHost, strlen($host));
            $maxPath = max($maxPath, strlen($route->getPattern()));

            if ($egzaktRequest = $route->getDefault('_egzaktRequest')) {
                if (isset($egzaktRequest['mappedRouteName'])) {
                    $maxMappingSource = max($maxMappingSource, strlen($egzaktRequest['mappedRouteName']));
                }
                if (isset($egzaktRequest['appSlug'])) {
                    $maxApplication = max($maxApplication, strlen($egzaktRequest['appSlug']));
                }
            }
        }

        $format  = '%-'.$maxName.'s %-'.$maxMethod.'s %-'.$maxScheme.'s %-'.$maxHost.'s %-'.$maxPath.'s %-'.$maxApplication.'s %s';
        $formatHeader  = '%-'.($maxName + 19).'s %-'.($maxMethod + 19).'s %-'.($maxScheme + 19).'s %-'.($maxHost + 19).'s %-'.($maxPath + 19).'s %-'.($maxApplication + 19).'s %s';
        $output->writeln(sprintf($formatHeader, '<comment>Name</comment>', '<comment>Method</comment>',  '<comment>Scheme</comment>', '<comment>Host</comment>', '<comment>Path</comment>', '<comment>Application</comment>', '<comment>Source</comment>'));

        foreach ($routes as $name => $route) {
            $method = $route->getMethods() ? implode('|', $route->getMethods()) : 'ANY';
            $scheme = $route->getSchemes() ? implode('|', $route->getSchemes()) : 'ANY';
            $host = '' !== $route->getHost() ? $route->getHost() : 'ANY';
            $mappingSource = '';
            $appSlug = '';
            if ($egzaktRequest = $route->getDefault('_egzaktRequest')) {
                if (isset($egzaktRequest['mappedRouteName'])) {
                    $mappingSource = $egzaktRequest['mappedRouteName'];
                }
                if (isset($egzaktRequest['appSlug'])) {
                    $appSlug = $egzaktRequest['appSlug'];
                }
            }
            $output->write(sprintf($format,  $name, $method, $scheme, $host, $route->getPath(), '<fg=yellow>' . $appSlug . '</>', $mappingSource), OutputInterface::OUTPUT_RAW);
        }
    }

}