<?php
namespace Stfalcon\Bundle\PortfolioBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;

class AddTranslatedFieldSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $options;
    private $container;

    /**
     * Init
     *
     * @param \Symfony\Component\Form\FormFactoryInterface              $factory
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array                                                     $options
     */
    public function __construct(FormFactoryInterface $factory, ContainerInterface $container, array $options)
    {
        $this->factory = $factory;
        $this->options = $options;
        $this->container = $container;
    }

    /**
     * @static
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data,
        // form.post_data and form.bind_norm_data event
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_BIND => 'postBind',
            FormEvents::BIND => 'bindNormData'
        );
    }


    /**
     * Validates the submitted form
     *
     * @param \Symfony\Component\Form\Event\DataEvent $event
     */
    public function bindNormData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $validator = $this->container->get('validator');

        foreach ($this->getFieldNames() as $locale => $fieldName) {
            $content = $form->get($fieldName)->getData();

            if (is_null($content) && in_array($locale, $this->options['required_locale'])) {
                $form->addError(
                    new FormError(
                        sprintf("Field '%s' for locale '%s' cannot be blank", $this->options['field'], $locale)
                    )
                );
            } else {
                $translation = $this->createPersonalTranslation($locale, $fieldName, $content);
                $errors = $validator->validate(
                    $translation,
                    array(sprintf("%s:%s", $this->options['field'], $locale))
                );

                if (count($errors) > 0) {
                    foreach($errors as $error) {
                        $form->addError(new FormError($error->getMessage()));
                    }
                }
            }
        }
    }

    /**
     * If the form passed the validation then set the corresponding Personal Translations
     *
     * @param \Symfony\Component\Form\Event\DataEvent $event
     */
    public function postBind(DataEvent $event)
    {
       $form = $event->getForm();
       $data = $form->getData();

       $entity = $form->getParent()->getData();

       foreach ($this->bindTranslations($data) as $binded) {
           $content = $form->get($binded['fieldName'])->getData();
           /** @var $translation \Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation */
           $translation = $binded['translation'];

           // Set the submitted content
           $translation->setContent($content);

           // Test if its new
           if ($translation->getId()) {
               // Delete the Personal Translation if its empty
               if (is_null($content) && $this->options['remove_empty']) {
                   $data->removeElement($translation);

                   if ($this->options['entity_manager_removal']) {
                       $this->container->get('doctrine.orm.entity_manager')->remove($translation);
                   }
               }
           } elseif (!is_null($content)) {
               // Add it to entity
               $entity->addTranslation($translation);

               if (!$data->contains($translation)) {
                   $data->add($translation);
               }
           }
       }
    }

    /**
     * Builds the custom 'form' based on the provided locales
     *
     * @param \Symfony\Component\Form\Event\DataEvent $event
     */
    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. We're only concerned with when
        // setData is called with an actual Entity object in it (whether new,
        // or fetched with Doctrine). This if statement let's us skip right
        // over the null condition.
        if (!is_null($data)) {
            foreach ($this->bindTranslations($data) as $binded) {
                $form->add(
                    $this->factory->createNamed(
                        $binded['fieldName'],
                        $this->options['widget'],
                        $binded['translation']->getContent(),
                        array(
                            'label' => $binded['locale'],
                            'required' => in_array(
                                $binded['locale'],
                                $this->options['required_locale']
                            ),
                            'property_path'=> false,
                        )
                    )
                );
            }
        }
    }

    /**
     * Small helper function to extract all Personal Translation
     * from the Entity for the field we are interested in
     * and combines it with the fields
     *
     * @param $data
     *
     * @return array
     */
    protected function bindTranslations($data)
    {
        $collection = array();
        $availableTranslations = array();

        foreach ($data as $translation) {
            if($translation->getField() == $this->options['field']) {
                $availableTranslations[$translation->getLocale()] = $translation;
            }
        }

        foreach ($this->getFieldNames() as $locale => $fieldName) {
            if (isset($availableTranslations[$locale])) {
                $translation = $availableTranslations[$locale];
            } else {
                $translation = $this->createPersonalTranslation(
                    $locale,
                    $this->options['field'],
                    null
                );
            }

            $collection[] = array(
                'locale'      => $locale,
                'fieldName'   => $fieldName,
                'translation' => $translation,
            );
        }

        return $collection;
    }

    /**
     * Helper function to generate all field names in format: '<locale>' => '<field>_<locale>'
     *
     * @return array
     */
    protected function getFieldNames()
    {
        $collection = array();

        foreach ($this->options['locales'] as $locale) {
            $collection[$locale] = $this->options['field'] ."_". $locale;
        }

        return $collection;
    }

    /**
     * Create personal translation
     *
     * @param $locale
     * @param $field
     * @param $content
     *
     * @return mixed
     */
    protected function createPersonalTranslation($locale, $field, $content)
    {
        //creates a new Personal Translation
        $className = $this->options['personal_translation'];

        return new $className($locale, $field, $content);
    }
}