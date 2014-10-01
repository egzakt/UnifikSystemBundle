<?php

namespace Unifik\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $parentData = $form->getParent()->getData();
        $fieldName = $view->vars['name'];
        $hasFile = false;
        $fieldWebPath = null;
        $fieldValue = null;

        if ($parentData) {
            $classUses = class_uses($parentData);

            if (false == in_array('Unifik\DoctrineBehaviorsBundle\Model\Uploadable\Uploadable', $classUses)) {
                throw new \Exception(
                    get_class($parentData) . ' must implement the Uploadable behaviour to be used in the advanced_file type.'
                );
            }

            $fieldValue = $parentData->{'get' . ucfirst($fieldName) . 'Path'}();
            $hasFile = (bool) $fieldValue;
            $fieldWebPath = $parentData->getWebPath($fieldName);
        }

        $view->vars['has_file'] = $hasFile;
        $view->vars['file_name'] = $fieldName;
        $view->vars['file_web_path'] = $fieldWebPath;
        $view->vars['file_value'] = $fieldValue;
        $view->vars['deletable'] = $options['deletable'];
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'deletable' => false
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
