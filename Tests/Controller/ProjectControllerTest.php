<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test cases for ProjectController
 */
class ProjectControllerTest extends WebTestCase
{

    /**
     * Get path to test project image
     *
     * @return string
     */
    private function _getTestImagePath()
    {
        return \realpath(__DIR__ . '/../Entity/Resources/files/projects/preorder-it/data/index.png');
    }

    public function testEmptyProjectsList()
    {
        $this->loadFixtures(array());
        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_project_list', array()), 'GET', true, true);

        // check don't display projects
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count());
    }

    public function testProjectsList()
    {
        $this->loadFixtures(array(
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
                ));
        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_project_list', array()), 'GET', true, true);

        // check display projects
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("preorder.it")')->count());
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("eprice.kz")')->count());
        $this->assertEquals(6, $crawler->filter('table tbody tr td:contains("example.com")')->count());
    }

    public function testCreateValidProject()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_project_create', array()));

        $form = $crawler->selectButton('Создать и редактировать')->form();

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $category = $em->getRepository("StfalconPortfolioBundle:Category")->findOneBy(array('slug' => 'web-development'));

        $formId = substr($form->getUri(), -14);

        $form[$formId . '[name]'] = 'wallpaper.in.ua';
        $form[$formId . '[slug]'] = 'wallpaper-in-ua';
        $form[$formId . '[url]'] = 'http://wallpaper.in.ua';
        $form[$formId . '[imageFile]'] = $this->_getTestImagePath();
        $form[$formId . '[description]'] = 'Free desktop wallpapers gallery.';
        $form[$formId . '[users]'] = 'users';
        $form[$formId . '[categories]']->select(array($category->getId()));
        $form[$formId . '[onFrontPage]'] = 1;
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('admin_bundle_portfolio_project_edit', array('id' => 1))));

        // @todo дальше лишние проверки. достаточно проверить или проект создался в БД
        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_project_list', array()), 'GET', true, true);
        // check display new category in list
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("wallpaper.in.ua")')->count());
    }

    public function testCreateInvalidProject()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testEditProject()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDeleteProject()
    {
        $this->loadFixtures(array(
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
                ));

        $client = $this->makeClient(true);
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository("StfalconPortfolioBundle:Project")->findOneBy(array('slug' => 'preorder-it'));

        // delete project
        $client->request('POST', $this->getUrl('admin_bundle_portfolio_project_delete', array('id' => $project->getId())), array('_method' => 'DELETE'));

        // check if project was removed from DB
        $em->detach($project);
        $projectRemoved = $em->getRepository("StfalconPortfolioBundle:Project")->findOneBy(array('slug' => 'preorder-it'));
        $this->assertNull($projectRemoved);
    }

    public function testDeleteNotExistProject()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $client->request('POST', $this->getUrl('admin_bundle_portfolio_project_delete', array('id' => 0)));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testViewProject()
    {
        $this->loadFixtures(array(
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
                ));

        $crawler = $this->fetchCrawler(
            $this->getUrl(
                'portfolio_project_view',
                array('categorySlug' => 'web-development', 'projectSlug' => 'preorder-it')
            ), 'GET', true, true);

        $description = "Press-releases and reviews of the latest electronic novelties. The possibility to leave a pre-order.";

        // check display project info
        $this->assertEquals(1, $crawler->filter('html:contains("preorder.it")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("' . $description . '")')->count());
        $this->assertEquals(1, $crawler->filter('a[href="http://preorder.it"]')->count());

        $epriceUrl = $this->getUrl(
            'portfolio_project_view',
            array('categorySlug' => 'web-development', 'projectSlug' => 'eprice-kz')
        );
        // check display prev/next project url
        $this->assertEquals(1, $crawler->filter('#content a[href="' . $epriceUrl . '"]')->count());

        // check display projects in services widget
//        $this->assertEquals(1, $crawler->filter('#sidebar a[href="' . $epriceUrl . '"]')->count());
    }

    public function testFilledProjectUsersList()
    {
        $this->loadFixtures(array(
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
        ));

        // Check project preorder.it
        $crawler = $this->fetchCrawler(
            $this->getUrl(
                'portfolio_project_view', array('categorySlug' => 'web-development', 'projectSlug' => 'preorder-it')
            ), 'GET', true, true);

        // check display project info
        $this->assertEquals(1, $crawler->filter('html:contains("Над проектом работали")')->count());
        $this->assertEquals(1, $crawler->filter('html #sidebar dl>dt:contains("art-director and designer")')->count());
    }

    public function testEmptyProjectUsersList()
    {
        $this->loadFixtures(array(
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
        ));

        // Check project eprice.kz
        $crawler = $this->fetchCrawler(
            $this->getUrl(
                'portfolio_project_view', array('categorySlug' => 'web-development', 'projectSlug' => 'eprice-kz')
            ), 'GET', true, true);


        // check display project info
        $this->assertEquals(0, $crawler->filter('html:contains("Над проектом работали")')->count());
        $this->assertEquals(0, $crawler->filter('html #sidebar dl>dt:contains("art-director and designer")')->count());
    }
}