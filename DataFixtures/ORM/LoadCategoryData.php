<?php

namespace Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;

/**
 * Categories fixtures
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Create and load categories fixtures to database
     *
     * @param Doctrine\ORM\EntityManager $em Entity manager object
     *
     * @return void
     */
    public function load($em)
    {
        // categories
        $development = new Category();
        $development->setName('Web Development');
        $development->setSlug('web-development');
        $development->setDescription('In work we use Symfony2.');

        $em->persist($development);
        $em->flush();

        $this->addReference('category-development', $development);
    }

    /**
     * Get the number for sorting fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}