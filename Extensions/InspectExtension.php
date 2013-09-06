<?php

namespace Egzakt\SystemBundle\Extensions;

/**
 * @author Christian Fecteau
 *
 * Inspect Twig templates with a debugger.
 *
 * Usages:
 *  {{ inspect() }}
 *  {{ inspect(myVar) }}
 */
class InspectExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'inspect' => new \Twig_Function_Method($this, 'inspect', array('needs_context' => true))
        );
    }

    /**
     * @param $context
     */
    public function inspect($context)
    {
        $this->breakPoint(1 === func_num_args() ? $context : func_get_arg(1));
    }

    /**
     * This where you set your breakpoint
     */
    protected function breakPoint($twig)
    {
        return; // breakpoint
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'egzakt_system_core.inspect';
    }
}
