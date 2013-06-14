<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
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
        if (null == $options['file_path_method']) {
            throw new MissingOptionsException('The "file_path_method" option must be set.');
        }

        $filePath = null;
        $parentData = $form->getParent()->getData();

        if (null !== $parentData) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $filePath = $accessor->getValue($parentData, $options['file_path_method']);
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
            'file_path_method' => null
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
