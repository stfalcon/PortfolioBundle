<?php

namespace Stfalcon\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Category Controller
 */
class CategoryController extends Controller
{
    /**
     * View category
     *
     * @param string $slug Category slug
     * @param int    $page Page number
     *
     * @return array
     *
     * @throws NotFoundHttpException
     *
     * @Route(
     *      "/portfolio/{slug}/{page}",
     *      name="portfolio_category_view",
     *      requirements={"page" = "\d+"},
     *      defaults={"page" = "1"}
     * )
     * @Template()
     */
    public function viewAction($slug, $page)
    {
        $category = $this->get('stfalcon_portfolio.category.repository')->findOneBy(array('slug' => $slug));

        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $query = $this->get('stfalcon_portfolio.project.repository')
            ->getQueryForSelectProjectsByCategory($category);

        $paginator = $this->get('knp_paginator')->paginate($query, $page, 6);
        $paginator->setUsedRoute('portfolio_category_view');

        if ($this->has('application_default.menu.breadcrumbs')) {
            $breadcrumbs = $this->get('application_default.menu.breadcrumbs');
            $breadcrumbs->addChild($category->getName())->setCurrent(true);
        }

        return array(
            'category' => $category,
            'paginator' => $paginator, // @todo переименовать переменную
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
        // @todo помоему этот блок отключен
        $categories = $this->get('stfalcon_portfolio.category.repository')->findAll();

        return array('categories' => $categories, 'currentProject' => $project, 'currentCategory' => $category);
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
        // @todo переименовать метод и роут
        // @todo перенести сортировку проектов в админку
        $em = $this->getDoctrine()->getManager();
        $projects = $this->getRequest()->get('projects');
        $projectManager = $this->get('stfalcon_portfolio.project.repository');
        foreach ($projects as $projectInfo) {
            $project = $projectManager->find($projectInfo['id']);
            $project->setOrdernum($projectInfo['index']);
            $em->persist($project);
        }
        $em->flush();

        return new Response('good');
    }
}