<?php

namespace Egzakt\SystemBundle\Generator;

/**
 * Entity Repository Generator
 */
class EntityRepositoryGenerator extends \Doctrine\ORM\Tools\EntityRepositoryGenerator
{
    /**
     * @var string|array $skeletonDirs
     */
    private $skeletonDirs;

    /**
     * @var bool $hasI18n
     */
    private $hasI18n;

    /**
     * Generate entity repository class
     *
     * @param string $fullClassName
     *
     * @return string
     */
    public function generateEntityRepositoryClass($fullClassName)
    {
        $namespace = substr($fullClassName, 0, strrpos($fullClassName, '\\'));
        $className = substr($fullClassName, strrpos($fullClassName, '\\') + 1, strlen($fullClassName));

        return $this->render('entity/EntityRepository.php.twig', array(
            'namespace' => $namespace,
            'classname' => $className,
            'has_i18n' => $this->hasI18n
        ));
    }

    /**
     * Render a Twig PHP template
     *
     * @param $template
     * @param $parameters
     *
     * @return string
     */
    protected function render($template, $parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->skeletonDirs), array(
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ));

        return $twig->render($template, $parameters);
    }

    /**
     * Sets an array of directories to look for templates.
     *
     * The directories must be sorted from the most specific to the most
     * directory.
     *
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs($skeletonDirs)
    {
        $this->skeletonDirs = is_array($skeletonDirs) ? $skeletonDirs : array($skeletonDirs);
    }

    /**
     * Will be used to include the Translatable trait or not.
     *
     * @param boolean $hasI18n
     */
    public function setHasI18n($hasI18n)
    {
        $this->hasI18n = $hasI18n;
    }
}
