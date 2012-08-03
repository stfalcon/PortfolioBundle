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
            ->add('name', 'translatable_field', array(
                'field'          => 'name',
                'property_path'  => 'translations',
                'personal_translation' => 'Stfalcon\Bundle\PortfolioBundle\Entity\ProjectTranslation',
            ))
            ->add('slug')
            ->add('url')
            ->add('description', 'translatable_field', array(
                'field'          => 'description',
                'widget'         => 'textarea',
                'attr'           => array('class' => 'controls'),
                'property_path'  => 'translations',
                'personal_translation' => 'Stfalcon\Bundle\PortfolioBundle\Entity\ProjectTranslation',
            ))
            ->add('imageFile', 'file', array('required' => false, 'data_class' => 'Symfony\Component\HttpFoundation\File\File'))
            ->add('date', 'date')
            ->add('categories')
            ->add('users', 'translatable_field', array(
                'field'          => 'users',
                'widget'         => 'textarea',
                'attr'           => array('class' => 'controls'),
                'property_path'  => 'translations',
                'personal_translation' => 'Stfalcon\Bundle\PortfolioBundle\Entity\ProjectTranslation',
            ))
            ->add('onFrontPage', 'checkbox', array('required' => false))
        ;
    }

    // @todo с sortable проблемы начиная со второй страницы (проекты перемещаются на первую страницу)
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('slug')
            ->add('name')
            ->add('date')
        ;
    }

    public function setTemplates(array $templates)
    {
        $templates['list'] = 'StfalconPortfolioBundle:ProjectAdmin:list.html.twig';
        parent::setTemplates($templates);
    }
}