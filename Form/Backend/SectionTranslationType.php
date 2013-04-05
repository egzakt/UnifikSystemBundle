<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Section Translation Type
 */
class SectionTranslationType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The Builder
     * @param array $options Array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active')
            ->add('name')
            ->add('slug')
            ->add('pageTitle', null, array('label' => 'Page title'))
            ->add('headCode', null, array('label' => 'Head code'));
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'section_translation';
    }

    /**
     * Get Default Options
     *
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Egzakt\SystemBundle\Entity\SectionTranslation'
        );
    }
}