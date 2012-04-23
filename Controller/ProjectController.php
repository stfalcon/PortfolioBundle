<?php

namespace Stfalcon\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Stfalcon\Bundle\PortfolioBundle\Form\ProjectForm;
use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * CRUD projects. Show users and nearby projects widget.
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
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
     * @Route("/portfolio/{categorySlug}/{projectSlug}", name="portfolioCategoryProjectView")
     * @Template()
     */
    public function viewAction($categorySlug, $projectSlug)
    {
        // try find category by slug
        $category = $this->_findCategoryBySlug($categorySlug);

        // try find project by slug
        $project = $this->_findProjectBySlug($projectSlug);

        if ($this->has('application_default.menu.breadcrumbs')) {
            $breadcrumbs = $this->get('application_default.menu.breadcrumbs');
            $breadcrumbs->addChild(
                $category->getName(),
                array(
                    'route' => 'portfolioCategoryView',
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
     * @Template()
     */
    public function nearbyProjectsAction($categorySlug, $projectSlug)
    {
        // try find category by slug
        $categorySlug = $this->_findCategoryBySlug($categorySlug);

        // try find project by slug
        $projectSlug = $this->_findProjectBySlug($projectSlug);

        $em = $this->get('doctrine')->getEntityManager();

        // get all projects from this category
        $projects = $em->getRepository("StfalconPortfolioBundle:Project")
                ->getProjectsByCategory($categorySlug);

        // get next and previous projects from this category
        $i = 0; $previousProject = null; $nextProject = null;
        foreach ($projects as $p) {
            if ($projectSlug->getId() == $p->getId()) {
                $previousProject = isset($projects[$i-1]) ? $projects[$i-1] : null;
                $nextProject     = isset($projects[$i+1]) ? $projects[$i+1] : null;
                break;
            }
            $i++;
        }

        return array('category' => $categorySlug, 'previousProject' => $previousProject, 'nextProject' => $nextProject);
    }

    /**
     * Try find category by slug
     *
     * @param string $slug Slug of category
     *
     * @return Category
     */
    private function _findCategoryBySlug($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $category = $em->getRepository("StfalconPortfolioBundle:Category")
                ->findOneBy(array('slug' => $slug));

        if (!$category) {
            throw new NotFoundHttpException('The category does not exist.');
        }

        return $category;
    }

    /**
     * Try find project by slug
     *
     * @param string $slug Slug of project
     *
     * @return Project
     */
    private function _findProjectBySlug($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // try find project by slug
        $project = $em->getRepository("StfalconPortfolioBundle:Project")
                ->findOneBy(array('slug' => $slug));
        if (!$project) {
            throw new NotFoundHttpException('The project does not exist.');
        }

        return $project;
    }
}
