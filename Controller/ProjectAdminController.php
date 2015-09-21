<?php

namespace Stfalcon\Bundle\PortfolioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;

class ProjectAdminController extends CRUDController
{
    /**
     * Ajax action
     * Change projects order
     *
     * @param Request $request
     *
     * @return string
     */
    public function changeProjectsOrderAction(Request $request)
    {
        $projects = $request->get('projects');
        $em = $this->get('doctrine')->getManager();

        foreach ($projects as $projectInfo) {
            $project = $em->getRepository("StfalconPortfolioBundle:Project")->find($projectInfo['id']);
            $project->setOrdernum($projectInfo['index']);
            $em->persist($project);
        }

        $em->flush();

        return new Response('good');
    }
}