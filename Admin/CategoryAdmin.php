<?php
namespace Stfalcon\Bundle\PortfolioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('slug')
            ->add('name', 'translatable_field', array(
                'field'          => 'name',
                'attr'           => array('class' => 'controls'),
                'property_path'  => 'translations',
                'personal_translation' => 'Stfalcon\Bundle\PortfolioBundle\Entity\CategoryTranslation',
            ))
            ->add('description', 'translatable_field', array(
                'field'          => 'description',
                'widget'         => 'textarea',
                'attr'           => array('class' => 'controls'),
                'property_path'  => 'translations',
                'personal_translation' => 'Stfalcon\Bundle\PortfolioBundle\Entity\CategoryTranslation',
            ))
            // @todo сделать сортировку через sortable (по аналогии с проектами)
            ->add('ordernum')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('slug')
            ->add('name')
        ;
    }
}