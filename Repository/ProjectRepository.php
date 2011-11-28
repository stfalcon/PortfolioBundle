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
                    JOIN p.categories c WHERE c.id = ?1 ORDER BY p.date DESC');
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
                    ORDER BY p.date DESC');

        return $query->getResult();
    }

}