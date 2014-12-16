<?php

namespace Unifik\SystemBundle\Form\Backend;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Unifik\DoctrineBehaviorsBundle\Form\MetadatableType;

/**
 * Section Translation Type
 */
class SectionTranslationType extends MetadatableType
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

        $builder
            ->add('active')
            ->add('name')
            ->add('slug')
        ;
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
            'data_class' => 'Unifik\SystemBundle\Entity\SectionTranslation'
        ));
    }
}
