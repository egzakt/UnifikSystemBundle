<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType
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
            ->add('translation', new RoleTranslationType())
        ;

        if (!$options['admin']) {
            $builder->add('sections', 'tree_choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'property' => 'name',
                    'class'    => 'Egzakt\SystemBundle\Entity\Section',
                    'required' => false
            ));
        }
    }

    public function getName()
    {
        return 'egzakt_backend_userbundle_roletype';
    }

    /**
     * Returns the default options for this type.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'data_class' => 'Egzakt\SystemBundle\Entity\Role',
            'admin' => false
        ));
    }
}
