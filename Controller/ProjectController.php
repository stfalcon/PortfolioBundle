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
     * Projects list
     *
     * @return array
     * @Route("/admin/portfolio/projects", name="portfolioProjectIndex")
     * @Template()
     */
    public function indexAction()
    {
        $projects = $this->get('doctrine')->getEntityManager()
            ->getRepository("StfalconPortfolioBundle:Project")->getAllProjects();

        return array('projects' => $projects);
    }

    /**
     * Create new project
     *
     * @return array|RedirectResponse
     * @Route("/admin/portfolio/project/create", name="portfolioProjectCreate")
     * @Template()
     */
    public function createAction()
    {
        $project = new Project();
        $project->setPathToUploads($this->_getUploadPath());

        $form = $this->get('form.factory')->create(new ProjectForm(), $project);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                // create project
                $em = $this->get('doctrine')->getEntityManager();
                $em->persist($project);
                $em->flush();

                $this->get('session')->setFlash('notice',
                    'Congratulations, your project "' . $project->getName()
                    . '" is successfully created!'
                );

                // redirect to list of projects
                return new RedirectResponse($this->generateUrl('portfolioProjectIndex'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Edit project
     *
     * @param string $slug Project slug
     *
     * @return array|RedirectResponse
     * @Route("/admin/portfolio/project/edit/{slug}", name="portfolioProjectEdit")
     * @Template()
     */
    public function editAction($slug)
    {
        $project = $this->_findProjectBySlug($slug);
        $project->setPathToUploads($this->_getUploadPath());

        $form = $this->get('form.factory')->create(new ProjectForm(), $project);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // save project
                $em = $this->get('doctrine')->getEntityManager();
                $em->persist($project);
                $em->flush();

                $this->get('session')->setFlash('notice',
                    'Congratulations, your project is successfully updated!'
                );

                return new RedirectResponse($this->generateUrl('portfolioProjectIndex'));
            }
        }

        return array('form' => $form->createView(), 'project' => $project);
    }

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
     * Delete project
     *
     * @param string $slug Slug of project
     *
     * @return RedirectResponse
     * @Route("/admin/portfolio/project/delete/{slug}", name="portfolioProjectDelete")
     */
    public function deleteAction($slug)
    {
        $project = $this->_findProjectBySlug($slug);

        $em = $this->get('doctrine')->getEntityManager();
        $em->remove($project);
        $em->flush();

        $this->get('session')->setFlash('notice',
            'Your project "' . $project->getName() . '" is successfully delete.'
        );

        return new RedirectResponse($this->generateUrl('portfolioProjectIndex'));
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

    /**
     * Get path to upload dir for project images
     *
     * @return string
     */
    private function _getUploadPath()
    {
        $uploadDir = '/uploads/portfolio/projects';

        return realpath($this->get('kernel')->getRootDir() . '/../web' . $uploadDir);
    }

}
