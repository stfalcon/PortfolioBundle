<?php

namespace Stfalcon\Bundle\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationLocaleType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['fields'] as $field => $type) {
            $builder->add($field, $type, array(
                'label'    => ucfirst($field),
                'required' => false,
                'attr'     => array(
                    'class' => 'span5'
                )
            ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true,
            'fields'         => array(),
        ));
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'project_translation_locale';
    }
}
