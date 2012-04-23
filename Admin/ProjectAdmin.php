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
            ->add('slug')
            ->add('name')
            ->add('description', 'textarea', array('attr' => array("class" => 'xxlarge')))
            ->add('url')
            ->add('date', 'datetime', array('required' => false))
            ->add('imageFile', 'file')
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

    public function setTemplates(array $templates)
    {
        $templates['list'] = 'StfalconPortfolioBundle::list.html.twig';
        parent::setTemplates($templates);
    }
}