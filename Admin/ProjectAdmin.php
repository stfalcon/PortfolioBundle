<?php
namespace Stfalcon\Bundle\PortfolioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class ProjectAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('slug')
            ->add('name')
            ->add('description', 'textarea', array('attr' => array("class" => 'xxlarge')))
            ->add('url')
            ->add('date')
            ->add('imageFile', 'file', array('required' => true))
            ->add('categories')
            ->add('users', 'textarea', array('attr' => array("class" => 'xxlarge')))
            ->add('onFrontPage', 'checkbox', array('required' => false))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('slug')
            ->add('name')
            ->add('description')
            ->add('date')
        ;
    }
}