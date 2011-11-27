<?php

namespace Stfalcon\Bundle\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * CategoryForm
 */
class CategoryForm extends AbstractType
{

    /**
     * Builds the form
     *
     * @param FormBuilder $builder The form builder
     * @param array       $options The options
     *
     * @return void
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
        $builder->add('slug');
        $builder->add('description', 'textarea');
    }

    /**
     * Returns the default options for this type
     *
     * @param array $options The options
     *
     * @return array The default options
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Stfalcon\Bundle\PortfolioBundle\Entity\Category',
        );
    }

    /**
     * Returns the name of this type
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'category';
    }

}