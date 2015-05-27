<?php

namespace Unifik\SystemBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Advanced Image Type
 */
class AdvancedImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'advanced_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'advanced_image';
    }
}
