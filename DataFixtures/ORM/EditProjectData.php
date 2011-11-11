<?php

namespace Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditProjectData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load($em)
    {
        // try find project by slug
        $preorder = $em->getRepository("StfalconPortfolioBundle:Project")
                ->findOneBy(array('slug' => 'preorder-it'));
        if (!$preorder) {
            throw new NotFoundHttpException('The project does not exist.');
        }

        // projects
        $preorder->setUsers(null);
        $em->persist($preorder);
        $em->flush();
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }

}