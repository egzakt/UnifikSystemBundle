<?php

namespace Flexy\SystemBundle\Manipulator;

use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class RoutingManipulator
 */
class RoutingManipulator extends Manipulator
{
    /**
     * @var string $file
     */
    private $file;

    /**
     * Constructor.
     *
     * @param string $file The YAML routing file path
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Adds a routing resource at the top of the existing ones.
     *
     * @param string $bundle
     * @param string $format
     * @param string $prefix
     * @param string $path
     * @param string $app
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If bundle is already imported
     */
    public function addResource($bundle, $format, $prefix = '/', $path = 'routing', $app = null)
    {
        $name = Container::underscore(substr($bundle, 0, -6));

        if (null === $app) {
            $name .= ('/' !== $prefix ? '_' . str_replace('/', '_', substr($prefix, 1)) : '');
        } else {
            $name .= '_' . $app;
        }

        $current = '';
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);

            // Don't add same route twice
            if (false !== strpos($current, $name)) {
                throw new \RuntimeException(sprintf('Bundle "%s" is already imported.', $bundle));
            }
        } elseif (!is_dir($dir = dirname($this->file))) {
            mkdir($dir, 0777, true);
        }

        $code = sprintf("%s:\n", $name);
        if ('annotation' == $format) {
            $code .= sprintf("    resource: \"@%s/Controller/\"\n    type:     annotation\n", $bundle);
        } else {
            $code .= sprintf("    resource: \"@%s/Resources/config/%s.%s\"\n", $bundle, $path, $format);
        }

        if (null !== $prefix) {
            $code .= sprintf("    prefix:   %s\n", $prefix);
        }

        $code .= "\n";
        $code .= $current;

        if (false === file_put_contents($this->file, $code)) {
            return false;
        }

        return true;
    }
}
