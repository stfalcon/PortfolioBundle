<?php

namespace Stfalcon\Bundle\PortfolioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;

/**
 * ProjectRepository
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class ProjectRepository extends EntityRepository
{
    /**
     * Get all projects from this category
     *
     * @param Category $category A category object
     *
     * @return array
     */
    public function getProjectsByCategory(Category $category)
    {
        $query = $this->getEntityManager()
                ->createQuery('SELECT p FROM StfalconPortfolioBundle:Project p
                    JOIN p.categories c WHERE c.id = ?1 ORDER BY p.ordernum ASC');
        $query->setParameter(1, $category->getId());

        return $query->getResult();
    }

    /**
     * Get all projects
     *
     * @return array
     */
    public function getAllProjects()
    {
        $query = $this->getEntityManager()
                ->createQuery('SELECT p FROM StfalconPortfolioBundle:Project p
                    ORDER BY p.ordernum ASC');

        return $query->getResult();
    }

    /**
     * Project Query For Pagination
     * @param int $categoryId
     * @return Doctrine\ORM\Query
     */
    public function getProjectsQueryForPagination($categoryId = 0)
    {
        return $this->createQueryBuilder('p')
                ->select('p')
                ->join('p.categories', 'c')
                ->where('c.id = ?1')
                ->orderBy('p.indexPage', 'ASC')
                ->setParameter(1, $categoryId)
                ->getQuery();
    }

    public function getIndexPageProjectsForCategory(Category $category)
    {
        $query = $this->getEntityManager()
                ->createQuery('SELECT p FROM StfalconPortfolioBundle:Project p
                    JOIN p.categories c WHERE c.id = ?1 AND p.indexPage = 1
                    ORDER BY p.ordernum ASC');
        $query->setParameter(1, $category->getId());

        return $query->getResult();
    }
}