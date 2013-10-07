<?php

namespace Flexy\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * TokenType
 */
class TokenType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('token', 'text', array('label' => 'Token', 'read_only' => true, 'attr' => array('class' => 'token')))
            ->add('translations', 'collection', array(
                'label' => false,
                'required' => false,
                'type' => new TokenTranslationType()
            ))
        ;

    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'token';
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flexy\SystemBundle\Entity\Token',
        ));
    }
}
