<?php

namespace Flexy\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * TranslationType
 */
class TokenListType extends AbstractType
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
            ->add('tokens', 'collection', array(
                'label' => false,
                'required' => false,
                'type' => new TokenType(),
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
        return 'token_list';
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flexy\SystemBundle\Entity\TokenList',
        ));
    }
}
