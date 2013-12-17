<?php
namespace Stfalcon\Bundle\PortfolioBundle\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;

/**
 * Category manager uses for work with categories
 */
class CategoryManager
{
    protected $objectManager;
    protected $class;
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectManager $om
     * @param string        $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
        $this->repository = $om->getRepository($class);
    }

    /**
     * Create new category
     *
     * @return mixed
     */
    public function create()
    {
        return new $this->class;
    }

    /**
     * Remove category
     *
     * @param mixed $category
     */
    public function delete($category)
    {
        $this->objectManager->remove($category);
        $this->objectManager->flush();
    }

    /**
     * Save category
     *
     * @param mixed $category
     */
    public function save($category)
    {
        $this->objectManager->persist($category);
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
     * Find one category by criteria
     *
     * @param array $criteria
     *
     * @return object
     */
    public function findCategoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Find categories by criteria
     *
     * @param array $criteria
     *
     * @return array
     */
    public function findCategoriesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Find all categories
     *
     * @return mixed
     */
    public function findAllCategories()
    {
        return $this->repository->findAll();
    }
}
