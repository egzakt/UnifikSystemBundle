<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * MemberType
 */
class MemberType extends AbstractType
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
            ->add('active')
            ->add('emailConfirmed', null, array('label' => 'Email confirmed'))
            ->add('firstname')
            ->add('lastname')
            ->add('address')
            ->add('city')
            ->add('postalCode', null, array('label' => 'Postal code'))
            ->add('telephone')
            ->add('password', 'repeated')
            ->add('email')
        ;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'member';
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Egzakt\SystemBundle\Entity\Member',
            'validation_groups' => array('edit')
        ));
    }
}
