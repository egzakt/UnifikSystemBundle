<?php

namespace Unifik\SystemBundle\Form\Backend;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType
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
            ->add('translation', new RoleTranslationType())
        ;

        if (!$options['admin']) {
            $builder->add('sections', 'tree_choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'property' => 'name',
                    'class'    => 'UnifikSystemBundle:Section',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                                ->leftJoin('s.app', 'a')
                                ->orderBy('a.ordering', 'ASC')
                                ->addOrderBy('s.ordering', 'ASC')
                            ;
                    },
                    'required' => false
            ));
        }
    }

    public function getName()
    {
        return 'unifik_backend_userbundle_roletype';
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
            'data_class' => 'Unifik\SystemBundle\Entity\Role',
            'admin' => false
        ));
    }
}
