<?php

namespace Stfalcon\Bundle\PortfolioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Stfalcon\Bundle\PortfolioBundle\Entity\BaseCategory;

/**
 * Project Repository
 */
class ProjectRepository extends EntityRepository
{

    /**
     * Get query for select projects by category
     *
     * @param BaseCategory $category
     *
     * @return Doctrine\ORM\Query
     */
    public function getQueryForSelectProjectsByCategory(BaseCategory $category)
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
     * @param BaseCategory $category A category object
     *
     * @return array
     */
    public function findProjectsByCategory(BaseCategory $category)
    {
        return $this->getQueryForSelectProjectsByCategory($category)
                ->getResult();
    }
}