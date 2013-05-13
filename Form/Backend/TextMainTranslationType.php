<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Text Main Translation Type
 */
class TextMainTranslationType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active')
            ->add('name')
            ->add('text', 'ckeditor')
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
     * Get Default Options
     *
     * @param array $options
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Egzakt\SystemBundle\Entity\TextTranslation',
        );
    }
}