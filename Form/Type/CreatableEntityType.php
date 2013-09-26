<?php

namespace Egzakt\SystemBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Creatable Entity Type
 */
class CreatableEntityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (null == $options['quick_create_route']) {
            throw new MissingOptionsException('The "quick_create_route" option must be set.');
        }

        $view->vars['quick_create_route'] = $options['quick_create_route'];

        $tokens = explode('\\', $options['class']);
        $view->vars['entity_name'] = array_pop($tokens);
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'quick_create_route' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'creatable_entity';
    }
}
