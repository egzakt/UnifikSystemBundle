<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Test Main Type
 */
class TextMainType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The builder
     * @param array                $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('collapsable')
            ->add('translation', new TextMainTranslationType())
        ;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'text';
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Egzakt\SystemBundle\Entity\Text',
            'cascade_validation' => true
        ));
    }
}
