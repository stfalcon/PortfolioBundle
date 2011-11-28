<?php

namespace Stfalcon\Bundle\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * ProjectForm
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class ProjectForm extends AbstractType
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
        $builder->add('url', 'text', array('required' => false));
        $builder->add('date', 'date');
        $builder->add('image', 'file', array('required' => false));
        $builder->add('description', 'textarea');
        $builder->add('users', 'textarea', array('required' => false));
        $builder->add('categories', 'entity', array(
                    'class' => 'Stfalcon\Bundle\PortfolioBundle\Entity\Category',
                    'multiple' => true, 'expanded' => true,
                ));
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
            'data_class' => 'Stfalcon\Bundle\PortfolioBundle\Entity\Project',
        );
    }

    /**
     * Returns the name of this type
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'project';
    }
}