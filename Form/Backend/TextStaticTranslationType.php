<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Text Static Translation Type
 */
class TextStaticTranslationType extends AbstractType
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
            ->add('active', 'checkbox')
            ->add('text', 'textarea', array('label' => 'Text', 'attr' => array('class' => 'ckeditor')))
        ;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'text_translation';
    }

    /**
     * Set default options
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Egzakt\SystemBundle\Entity\TextTranslation',
        ));
    }
}
