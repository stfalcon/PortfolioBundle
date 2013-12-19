<?php

namespace Stfalcon\Bundle\PortfolioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;

/**
 * Project Repository
 */
abstract class ProjectRepository extends EntityRepository
{

    /**
     * Get query for select projects by category
     *
     * @param Category $category
     *
     * @return Doctrine\ORM\Query
     */
    public function getQueryForSelectProjectsByCategory(Category $category)
    {
        return $this->createQueryBuilder('p')
                ->select('p')
                ->join('p.categories', 'c')
                ->where('c = :category')
                ->orderBy('p.ordernum', 'ASC')
                ->setParameter('category', $category)
                ->getQuery();
    }

    /**
     * Get all projects from this category
     *
     * @param Category $category A category object
     *
     * @return array
     */
    public function findProjectsByCategory(Category $category)
    {
        return $this->getQueryForSelectProjectsByCategory($category)
                ->getResult();
    }
}