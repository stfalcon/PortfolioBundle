<?php
namespace Stfalcon\Bundle\PortfolioBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Project manager uses for work with projects
 */
class ProjectManager
{
    protected $objectManager;
    protected $class;
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityManager $om
     * @param string        $class
     */
    public function __construct(EntityManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * Create new project
     *
     * @return mixed
     */
    public function create()
    {
        return new $this->class;
    }

    /**
     * Remove project
     *
     * @param mixed $project
     */
    public function delete($project)
    {
        $this->objectManager->remove($project);
        $this->objectManager->flush();
    }

    /**
     * Save project
     *
     * @param mixed $project
     */
    public function save($project)
    {
        $this->objectManager->persist($project);
        $this->objectManager->flush();
    }

    /**
     * Get class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Find all project by category
     *
     * @param mixed $category
     *
     * @return array
     */
    public function findProjectsByCategory($category)
    {
        $qb = $this->repository->createQueryBuilder('p');
        $projects = $qb->join('p.categories', 'c')
            ->where('c = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->execute();

        return $projects;
    }

    /**
     * Find one project by criteria
     *
     * @param array $criteria
     *
     * @return object
     */
    public function findProjectBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Find projects by criteria
     *
     * @param array $criteria
     *
     * @return array
     */
    public function findProjectsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Find all projects
     *
     * @return mixed
     */
    public function findAllProjects()
    {
        return $this->repository->findAll();
    }

    /**
     * Get query for select projects by category
     *
     * @param Category $category
     *
     * @return Query
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
}
