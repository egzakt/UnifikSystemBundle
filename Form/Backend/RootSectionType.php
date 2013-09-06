<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\FormBuilderInterface;

use Egzakt\SystemBundle\Form\Backend\SectionType;

/**
 * RootSection Type
 */
class RootSectionType extends SectionType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The Builder
     * @param array                $options Array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'root_section';
    }
}
