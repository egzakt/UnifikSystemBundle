<?php

namespace Unifik\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * LocaleType
 */
class LocaleType extends AbstractType
{

    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active')
            ->add('name', null, array('attr' => array('alt' => 'Displayed on the language switcher')))
            ->add('code', 'locale', array('label' => 'Language', 'preferred_choices' => array('en', 'fr', 'es')))
        ;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'locale';
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Unifik\SystemBundle\Entity\Locale',
        ));
    }
}
