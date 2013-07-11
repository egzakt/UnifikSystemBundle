<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Egzakt\SystemBundle\Lib\BaseEntityRepository;

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
            ->add('active', null, array('disabled' => $options['self_edit']))
            ->add('username')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat')
            ))
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('userRoles', 'entity', array(
                'class' => 'EgzaktSystemBundle:Role',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Roles',
                'query_builder' => function(BaseEntityRepository $repo) use ($options) {
                    $repo->setReturnQueryBuilder(true);

                    if ($options['developer']) {
                        return $repo->findAllExcept('ROLE_BACKEND_ACCESS');
                    } else {
                        return $repo->findAllExcept(array('ROLE_DEVELOPER', 'ROLE_BACKEND_ACCESS'));
                    }
                }
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Egzakt\SystemBundle\Entity\User',
            'self_edit' => false,
            'developer' => false,
            'error_mapping' => array('roles' => 'userRoles')
        ));
    }
}
