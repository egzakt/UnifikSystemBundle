<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * Set default options
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Egzakt\SystemBundle\Entity\SectionTranslation'
        ));
    }
}