<?php

namespace Stfalcon\Bundle\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
     * List of categories
     *
     * @return array
     * @Route("/admin/portfolio/categories", name="portfolioCategoryIndex")
     * @Template()
     */
    public function indexAction()
    {
        $categories = $this->get('doctrine')->getEntityManager()
                ->getRepository("StfalconPortfolioBundle:Category")->getAllCategories();

        return array('categories' => $categories);
    }

    /**
     * Create new category
     *
     * @return array|RedirectResponse
     * @Route("/admin/portfolio/category/create", name="portfolioCategoryCreate")
     * @Template()
     */
    public function createAction()
    {
        $category = new Category();
        $form = $this->get('form.factory')->create(new CategoryForm(), $category);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));
            if ($form->isValid()) {
                $em = $this->get('doctrine')->getEntityManager();
                $em->persist($category);
                $em->flush();

                $this->get('session')->setFlash('notice',
                    'Congratulations, your category is successfully created!'
                );
                return new RedirectResponse($this->generateUrl('portfolioCategoryIndex'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Edit category
     *
     * @param string $slug Category slug
     *
     * @return array|RedirectResponse
     * @Route("/admin/portfolio/category/edit/{slug}", name="portfolioCategoryEdit")
     * @Template()
     */
    public function editAction($slug)
    {
        // try find category by slug
        $category = $this->_findCategoryBySlug($slug);
        $form = $this->get('form.factory')->create(new CategoryForm(), $category);

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));
            if ($form->isValid()) {
                // save category
                $em = $this->get('doctrine')->getEntityManager();
                $em->persist($category);
                $em->flush();

                $this->get('session')->setFlash('notice', 'Congratulations, your category is successfully updated!');
                return new RedirectResponse($this->generateUrl('portfolioCategoryIndex'));
            }
        }

        return array('form' => $form->createView(), 'category' => $category);
    }

    /**
     * View category
     *
     * @param string $slug Category slug
     *
     * @return array
     * @Route("/portfolio/{slug}", name="portfolioCategoryView")
     * @Template()
     */
    public function viewAction($slug)
    {
        $category = $this->_findCategoryBySlug($slug);

        $paginator = \Zend\Paginator\Paginator::factory($category->getProjects()->toArray());
        $paginator->setCurrentPageNumber($this->get('request')->query->get('page', 1));
        $paginator->setItemCountPerPage(6);

        $breadcrumbs = $this->get('menu.breadcrumbs');
        $breadcrumbs->addChild('Услуги', $this->get('router')->generate('homepage'));
        $breadcrumbs->addChild($category->getName())->setIsCurrent(true);

        return array(
            'category' => $category,
            'paginator' => $paginator,
        );
    }

    /**
     * Delete category
     *
     * @param string $slug Category slug
     *
     * @return RedirectResponse
     * @Route("/admin/portfolio/category/delete/{slug}", name="portfolioCategoryDelete")
     */
    public function deleteAction($slug)
    {
        $category = $this->_findCategoryBySlug($slug);

        $em = $this->get('doctrine')->getEntityManager();
        $em->remove($category);
        $em->flush();

        $this->get('session')->setFlash('notice', 'Your category is successfully delete.');

        return new RedirectResponse($this->generateUrl('portfolioCategoryIndex'));
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
     * Try find category by slug
     *
     * @param string $slug Category slug
     *
     * @return Category
     */
    private function _findCategoryBySlug($slug)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $category = $em->getRepository("StfalconPortfolioBundle:Category")
                ->findOneBy(array('slug' => $slug));
        if (!$category) {
            throw new NotFoundHttpException('The category does not exist.');
        }

        return $category;
    }

}
