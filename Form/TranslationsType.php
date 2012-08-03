<?php

namespace Stfalcon\Bundle\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Stfalcon\Bundle\PortfolioBundle\Entity\ProjectTranslation;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\FileCacheReader;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Collections\ArrayCollection;

class TranslationsType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\Common\Annotations\FileCacheReader
     */
    private $annotationReader;

    /**
     * @var \Gedmo\Translatable\TranslatableListener
     */
    private $translatableListener;

    /**
     * FormType constructor
     *
     * @param EntityManager        $em
     * @param FileCacheReader      $annotationReader
     * @param TranslatableListener $translatableListener
     */
    public function __construct(EntityManager $em,
                                FileCacheReader $annotationReader,
                                TranslatableListener $translatableListener
    )
    {
        $this->em                   = $em;
        $this->annotationReader     = $annotationReader;
        $this->translatableListener = $translatableListener;
    }

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

        $projectTranslationClass = $builder->getParent()->getDataClass();

        $configuration = $this->translatableListener->getConfiguration($this->em, $projectTranslationClass);

        $projectClass = $configuration['useObjectClass'];

        $fields = array();
        foreach ($configuration['fields'] as $field) {
            $annotations          = $this->annotationReader->getPropertyAnnotations(new \ReflectionProperty($projectClass, $field));
            $mappingColumn        = array_filter($annotations, function($item)
            {
                return $item instanceof \Doctrine\ORM\Mapping\Column;
            });
            $mappingColumnCurrent = current($mappingColumn);
            // Convert field type
            switch ($mappingColumnCurrent->type) {
                case 'string':
                    $fields[$field] = 'text';
                    break;
                case 'text':
                    $fields[$field] = 'textarea';
                    break;
            }
        }

        // Build sub form for the each locale
        foreach ($options['locales'] as $locale) {
            $builder->add($locale, 'project_translation_locale', array(
                'fields' => $fields
            ));
        }

        //Add event listeners
        $this->preSetEventHandler($builder);

        $this->bindEventHandler($builder, $configuration);
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => array()
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_translations';
    }


    /**
     * Bind event handler
     *
     * @param FormBuilderInterface $builder
     * @param array                $configuration
     */
    private function bindEventHandler(FormBuilderInterface $builder, $configuration)
    {
        $builder->addEventListener(FormEvents::BIND, function(FormEvent $event) use ($builder, $configuration)
        {
            $form = $event->getForm();
            $data = $event->getData();

            if (is_array($data)) {
                $data = new ArrayCollection();

            } else {
                // Remove new elements with wrong format
                foreach ($data as $key => $d) {
                    if (!is_numeric($key)) {
                        $data->removeElement($d);
                    }
                }
            }

            // Add/Update new elements with right format
            $newData = new ArrayCollection();
            foreach ($form->getChildren() as $translationsLocaleForm) {
                $locale = $translationsLocaleForm->getName();
                foreach ($translationsLocaleForm->getChildren() as $translation) {
                    $field   = $translation->getName();
                    $content = $translation->getData();

                    $existingTranslationEntity = $data->filter(function($entity) use ($locale, $field)
                    {
                        return ($entity->getLocale() === $locale && $entity->getField() === $field);
                    })->first();

                    if ($existingTranslationEntity) {
                        $existingTranslationEntity->setContent($content);
                        $newData->add($existingTranslationEntity);
                    } else {
                        $translationEntity = new ProjectTranslation();
                        $translationEntity->setLocale($locale);
                        $translationEntity->setField($field);
                        $translationEntity->setContent($content);
                        $newData->add($translationEntity);
                    }
                }
            }

            $event->setData($newData);
        });
    }

    /**
     * Pre-set event handler
     *
     * @param FormBuilderInterface $builder
     */
    private function preSetEventHandler(FormBuilderInterface $builder)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($builder)
        {
            $form = $event->getForm();
            $data = $event->getData();

            if (is_null($data)) {
                return;
            }

            // Sort by locales and fields
            $dataLocale = array();
            foreach ($data as $item) {
                if (!isset($dataLocale[$item->getLocale()])) {
                    $dataLocale[$item->getLocale()] = new ArrayCollection();
                }
                $dataLocale[$item->getLocale()][$item->getField()] = $item;
            }

            foreach ($form->getChildren() as $translationFields) {
                $locale = $translationFields->getName();
                if (isset($dataLocale[$locale])) {
                    foreach ($translationFields as $translationField) {
                        $field = $translationField->getName();
                        if (isset($dataLocale[$locale][$field])) {
                            $translationField->setData($dataLocale[$locale][$field]->getContent());
                        }
                    }
                }
            }
        });
    }
}

