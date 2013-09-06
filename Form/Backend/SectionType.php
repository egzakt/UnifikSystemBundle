<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Section Type
 */
class SectionType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The Builder
     * @param array                $options Array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translation', new SectionTranslationType())
            ->add('parent', null, array(
                'empty_value' => '',
                'empty_data' => null,
                'required' => false,
                'query_builder' => function(EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('s');

                    if ($options['current_section'] && $options['current_section']->getId()) {
                        $qb->where('s.id <> :current_section');
                        $qb->setParameter(':current_section', $options['current_section']->getId());
                    }

                    $qb->andWhere('s.app = :appId');
                    $qb->setParameter(':appId', $options['managed_app']->getId());

                    return $qb;
                }
            ))
            ->add('app', null, array(
                'label' => 'Application',
                'required' => false,
                'empty_value' => false,
                'class' => 'EgzaktSystemBundle:App',
                'query_builder' => function(EntityRepository $er) {
                    $qb = $er->createQueryBuilder('a')
                        ->where('a.id <> 1')
                        ->orderBy('a.ordering', 'ASC');

                    return $qb;
                }
            ))
            ->add('navigations', 'entity', array(
                'multiple' => true,
                'expanded' => true,
                'class' => 'EgzaktSystemBundle:Navigation',
                'required' => false,
                'query_builder' => function(EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('n');

                    $qb->andWhere('n.app = :appId');
                    $qb->setParameter(':appId', $options['managed_app']->getId());

                    // excluding internal navigations that starts with an underscore
                    $qb->andWhere($qb->expr()->neq($qb->expr()->substring('n.name', 1, 1), ':prefix'));
                    $qb->setParameter(':prefix', '_');

                    return $qb;
                },
            ));
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'section';
    }

    /**
     * Set Default Options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'data_class' => 'Egzakt\SystemBundle\Entity\Section',
            'current_section' => null,
            'managed_app' => null
        ));
    }
}
