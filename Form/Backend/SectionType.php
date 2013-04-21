<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Section Type
 */
class SectionType extends AbstractType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The Builder
     * @param array $options Array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translation', new SectionTranslationType())
            ->add('parent', null, array('empty_value' => '', 'empty_data' => null, 'required' => false))
            ->add('app', null, array('required' => false, 'empty_value' => false))
            ->add('navigations', 'entity', array(
                'multiple' => true,
                'expanded' => true,
                'class' => 'EgzaktSystemBundle:Navigation',
                'property' => 'name'
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
     * Get Default Options
     *
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
	    'cascade_validation' => true,
            'data_class' => 'Egzakt\SystemBundle\Entity\Section'
        );
    }
}