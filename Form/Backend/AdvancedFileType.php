<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Advanced File Type
 */
class AdvancedFileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $filePath = null;

        if (null !== $options['file_path']) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $filePath = $accessor->getValue($parentData, $options['file_path']);
            }
        }

        $view->vars['file_path'] = $filePath;
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'file_path' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'advanced_file';
    }
}
