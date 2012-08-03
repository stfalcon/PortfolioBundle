<?php

namespace Stfalcon\Bundle\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Stfalcon\Bundle\PortfolioBundle\Form\EventListener\AddTranslatedFieldSubscriber;

class TranslatedFieldType extends AbstractType
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Init
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Build translate field
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     *
     * @throws \InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(!class_exists($options['personal_translation'])) {
            Throw new \InvalidArgumentException(sprintf("Unable to find personal translation class: '%s'", $options['personal_translation']));
        }

        if(!$options['field']) {
            Throw new \InvalidArgumentException("You should provide a field to translate");
        }

        $subscriber = new AddTranslatedFieldSubscriber(
            $builder->getFormFactory(),
            $this->container,
            $options
        );

        $builder->addEventSubscriber($subscriber);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options = array())
    {
        //Personal Translations without content are removed
        $options['remove_empty'] = true;
        //Personal Translation class
        $options['personal_translation'] = false;
        // @todo move to config
        //the locales you wish to edit
        $options['locales'] = array('ru', 'en');
        //the required locales cannot be blank
        $options['required_locale'] = array('ru');
        //the field that you wish to translate
        $options['field'] = false;
        //change this to another widget like 'textarea' if needed
        $options['widget'] = "text";
        //auto removes the Personal Translation thru entity manager
        $options['entity_manager_removal'] = true;
        $options['csrf_protection'] = false;

        return $options;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translatable_field';
    }
}