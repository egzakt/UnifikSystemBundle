<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * User Type
 */
class UserType extends AbstractType
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
            ->add('username')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'options' => array('required' => $options['new']),
                'invalid_message' => 'The password fields must match.',
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat')
            ))
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('userroles', 'entity', array(
                'class' => 'EgzaktSystemBundle:Role',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles'
            ));
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

    /**
     * Returns the default options for this type.
     *
     * @param array $options
     *
     * @return array The default options
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'new' => false
        );
    }
}
