<?php

namespace Stfalcon\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;

/**
 * Project controller
 */
class ProjectController extends Controller
{
    /**
     * View project
     *
     * @param string $categorySlug Slug of category
     * @param string $projectSlug  Slug of project
     *
     * @return array
     *
     * @throws NotFoundHttpException
     *
     * @Route("/portfolio/{categorySlug}/{projectSlug}", name="portfolio_project_view")
     * @Template()
     */
    public function viewAction($categorySlug, $projectSlug)
    {
        // @todo упростить когда что-то разрулят с этим PR https://github.com/sensio/SensioFrameworkExtraBundle/pull/42

        // try find category by slug
        $category = $this->get('stfalcon_portfolio.category.repository')->findOneBy(array('slug' => $categorySlug));
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }
        // try find project by slug
        $project = $this->get('stfalcon_portfolio.project.repository')->findOneBy(array('slug' => $projectSlug));
        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        if ($this->has('application_default.menu.breadcrumbs')) {
            $breadcrumbs = $this->get('application_default.menu.breadcrumbs');
            $breadcrumbs->addChild(
                $category->getName(),
                array(
                    'route' => 'portfolio_category_view',
                    'routeParameters' => array('slug' => $category->getSlug())
                )
            );
            $breadcrumbs->addChild($project->getName())->setCurrent(true);
        }

        return array('project' => $project, 'category' => $category);
    }

    /**
     * Display links to prev/next projects
     *
     * @param string $categorySlug Object of category
     * @param string $projectSlug  Object of project
     *
     * @return array
     *
     * @throws NotFoundHttpException
     *
     * @Template()
     */
    public function nearbyProjectsAction($categorySlug, $projectSlug)
    {
        $category = $this->get('stfalcon_portfolio.category.repository')->findOneBy(array('slug' => $categorySlug));

        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }
        // try find project by slug
        $project = $this->get('stfalcon_portfolio.project.repository')->findOneBy(array('slug' => $projectSlug));

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }
        // get all projects from this category
        $projects = $this->get('stfalcon_portfolio.project.repository')->findProjectsByCategory($category);

        // get next and previous projects from this category
        $i = 0; $previousProject = null; $nextProject = null;
        foreach ($projects as $p) {
            if ($project->getId() == $p->getId()) {
                $previousProject = isset($projects[$i-1]) ? $projects[$i-1] : null;
                $nextProject     = isset($projects[$i+1]) ? $projects[$i+1] : null;
                break;
            }
            $i++;
        }

        return array('category' => $category, 'previousProject' => $previousProject, 'nextProject' => $nextProject);
    }
}
