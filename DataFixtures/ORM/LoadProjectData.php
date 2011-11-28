<?php

namespace Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Stfalcon\Bundle\PortfolioBundle\Entity\Project;

/**
 * Projects fixtures
 */
class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Create and load projects fixtures to database
     *
     * @param Doctrine\ORM\EntityManager $em Entity manager object
     *
     * @return void
     */
    public function load($em)
    {
        // projects
        $preorder = new Project();
        $preorder->setName('preorder.it');
        $preorder->setSlug('preorder-it');
        $preorder->setUrl('http://preorder.it');
        $preorder->setDate(new \DateTime('now'));
        $preorder->setDescription('Press-releases and reviews of the latest electronic novelties. The possibility to leave a pre-order.');
        $preorder->setUsers('<dl><dt>арт-директор и дизайнер</dt><dd>Олег Пащенко</dd></dl>');
        $preorder->addCategory($em->merge($this->getReference('category-development')));
        $em->persist($preorder);

        $eprice = new Project();
        $eprice->setName('eprice.kz');
        $eprice->setSlug('eprice-kz');
        $eprice->setUrl('http://eprice.kz');
        $eprice->setDate(new \DateTime('now'));
        $eprice->setDescription('Comparison of the prices of mobile phones, computers, monitors, audio and video in Kazakhstan');
        $eprice->addCategory($em->merge($this->getReference('category-development')));
        $em->persist($eprice);

        $em->flush();

        $this->addReference('project-preorder', $preorder);
        $this->addReference('project-eprice', $eprice);
    }

    /**
     * Get order number
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}