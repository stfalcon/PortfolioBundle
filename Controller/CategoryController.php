<?php

namespace Stfalcon\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;
use Stfalcon\Bundle\PortfolioBundle\Form\CategoryForm;

/**
 * CRUD categories. Services widget.
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class CategoryController extends Controller
{
    /**
     * View category
     *
     * @param Category $category
     * @param int $page
     *
     * @return array
     * @Route(
     *      "/portfolio/{slug}/{page}",
     *      name="portfolioCategoryView",
     *      requirements={"page" = "\d+"},
     *      defaults={"page" = "1"}
     * )
     * @Template()
     */
    public function viewAction(Category $category)
    {
        $knpPaginator = $this->get('knp_paginator');
        $paginator = $knpPaginator->paginate(
            $this->get('doctrine.orm.entity_manager')
                ->getRepository("StfalconPortfolioBundle:Project")
                ->getProjectsQueryForPagination($category->getId()),
            $this->getRequest()->get('page', 1) /*page number*/,
            6 /*limit per page*/
        );
        $paginator->setUsedRoute('portfolioCategoryView');

        if ($this->has('application_default.menu.breadcrumbs')) {
            $breadcrumbs = $this->get('application_default.menu.breadcrumbs');
            $breadcrumbs->addChild('Услуги', array('route' => 'homepage'));
            $breadcrumbs->addChild($category->getName())->setCurrent(true);
        }

        return array(
            'category' => $category,
            'paginator' => $paginator,
        );
    }

    /**
     * Services widget
     *
     * @param Category $category Category object
     * @param Project  $project  Project object
     *
     * @return array
     * @Template()
     */
    public function servicesAction(Category $category, $project = null)
    {
        $categories = $this->get('doctrine.orm.entity_manager')
                ->getRepository("StfalconPortfolioBundle:Category")->getAllCategories();

        return array('categories' => $categories, 'currentProject' => $project, 'currentCategory' => $category);
    }

    /**
     * Show projects by category
     *
     * @param Category $category
     *
     * @return array
     * @Route("/admin/portfolio/category/{slug}/projects", name="portfolioProjectsByCategory")
     * @Template()
     */
    public function projectsAction(Category $category)
    {
        return array(
            'category' => $category,
        );
    }

    /**
     * Ajax order projects
     *
     * @return string
     * @Route("/admin/portfolio/category/applyOrder", name="portfolioProjectsApplyOrder")
     * @Method({"POST"})
     */
    public function orderProjects()
    {
        $projects = $this->getRequest()->get('projects');
        $em = $this->get('doctrine')->getEntityManager();
        foreach ($projects as $projectInfo) {
            $project = $em->getRepository("StfalconPortfolioBundle:Project")->find($projectInfo['id']);
            $project->setOrdernum($projectInfo['index']);
            $em->persist($project);
        }
        $em->flush();

        return new Response('good');
    }
}