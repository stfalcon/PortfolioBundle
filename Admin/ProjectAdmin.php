<?php
namespace Stfalcon\Bundle\PortfolioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class ProjectAdmin extends Admin
{
    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);

        if (!$this->hasRequest()) {
            $this->datagridValues = array(
                '_page' => 1,
                '_sort_order' => 'ASC', // sort direction
                '_sort_by' => 'ordernum' // field name
            );
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('name')
                ->add('slug')
                ->add('description')
                ->add('translations', 'project_translations', array(
                    'by_reference' => false,
                    'attr' => array(
                        'class'   => 'project-translations',
                    ),
                    'locales' => array('uk', 'en')
                ))

            ->add('url')
            ->with('Options')
                ->add('imageFile', 'file', array('required' => false, 'data_class' => 'Symfony\Component\HttpFoundation\File\File'))
                ->add('date', 'date')
                ->add('categories')
                ->add('users')
                ->add('onFrontPage', 'checkbox', array('required' => false))
            ->end();
        ;
    }

    // @todo с sortable проблемы начиная со второй страницы (проекты перемещаются на первую страницу)
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('slug')
            ->add('name')
            ->add('date')
            ->add('translations', 'text', array(
                'template' => 'StfalconPortfolioBundle:ProjectAdmin:list_translations_field.html.twig'
            ))
        ;
    }

    public function setTemplates(array $templates)
    {
        $templates['list'] = 'StfalconPortfolioBundle:ProjectAdmin:list.html.twig';
        parent::setTemplates($templates);
    }
}