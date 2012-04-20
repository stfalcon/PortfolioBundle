<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test cases for ProjectController
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
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
        $this->assertEquals(7, $crawler->filter('table tbody tr td:contains("eprice.kz")')->count());
    }

    public function testCreateValidProject()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_project_create', array()));

        $inputs = $crawler->filter('form input');
        $inputs->first();
        $formId = str_replace("_slug", "", $inputs->current()->getAttribute('id'));

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $category = $em->getRepository("StfalconPortfolioBundle:Category")->findOneBy(array('slug' => 'web-development'));

        $form = $crawler->selectButton('Создать и редактировать')->form();

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
//        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('admin_bundle_portfolio_project_edit', array('id' => 1) )));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_project_list', array()), 'GET', true, true);
        // check display new category in list
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("wallpaper.in.ua")')->count());
    }

//    public function testCreateInvalidProject()
//    {
//    }
//
//    public function testEditProject()
//    {
//    }
//
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
        $crawler = $client->request('POST', $this->getUrl('admin_bundle_portfolio_project_delete', array('id' => $project->getId())), array('_method' => 'DELETE'));

        // check redirect to list of projects
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('admin_bundle_portfolio_project_list', array())));

        // check notice
//        $this->assertTrue($client->getRequest()->getSession()->hasFlash('notice'));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        // check don't display deleting category
        $this->assertEquals(0, $crawler->filter('table tbody tr td:contains("preorder-it")')->count());
    }

    public function testDeleteNotExistProject()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('POST', $this->getUrl('admin_bundle_portfolio_project_delete', array('id' => 0)));

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
                        'portfolioCategoryProjectView',
                        array('categorySlug' => 'web-development', 'projectSlug' => 'preorder-it')
                ), 'GET', true, true);

        $description = "Press-releases and reviews of the latest electronic novelties. The possibility to leave a pre-order.";

        // check display project info
        $this->assertEquals(1, $crawler->filter('html:contains("preorder.it")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("' . $description . '")')->count());
        $this->assertEquals(1, $crawler->filter('a[href="http://preorder.it"]')->count());

        $epriceUrl = $this->getUrl('portfolioCategoryProjectView',
                array('categorySlug' => 'web-development', 'projectSlug' => 'eprice-kz'));
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
                        'portfolioCategoryProjectView', array('categorySlug' => 'web-development', 'projectSlug' => 'preorder-it')
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
                        'portfolioCategoryProjectView', array('categorySlug' => 'web-development', 'projectSlug' => 'eprice-kz')
                ), 'GET', true, true);


        // check display project info
        $this->assertEquals(0, $crawler->filter('html:contains("Над проектом работали")')->count());
        $this->assertEquals(0, $crawler->filter('html #sidebar dl>dt:contains("art-director and designer")')->count());
     }
}