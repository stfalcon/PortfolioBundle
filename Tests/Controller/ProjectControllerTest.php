<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test cases for CategoryController
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
        $this->loadFixtures(array(), false);
        $crawler = $this->fetchCrawler($this->getUrl('portfolioProjectIndex', array()), 'GET', true, true);

        // check display notice
        $this->assertEquals(1, $crawler->filter('html:contains("List of projects is empty")')->count());
        // check don't display projects
        $this->assertEquals(0, $crawler->filter('ul li:contains("preorder.it")')->count());
    }

    public function testProjectsList()
    {
        $this->loadFixtures(array(
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
                    'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData',
                ));
        $crawler = $this->fetchCrawler($this->getUrl('portfolioProjectIndex', array()), 'GET', true, true);

        // check display projects
        $this->assertEquals(1, $crawler->filter('ul li:contains("preorder.it")')->count());
        $this->assertEquals(1, $crawler->filter('ul li:contains("eprice.kz")')->count());
    }

    public function testCreateValidProject()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioProjectCreate', array()));

        $form = $crawler->selectButton('Send')->form();

        $crawler = $client->submit($form, array(
            'project[name]' => 'wallpaper.in.ua',
            'project[slug]' => 'wallpaper-in-ua',
            'project[url]'  => 'http://wallpaper.in.ua',
            'project[image]'  => $this->_getTestImagePath(),
            'project[description]'  => 'Free desktop wallpapers gallery.',
        ));

        // check redirect to list of categories
//        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('portfolioProjectIndex', array())));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        // check display new category in list
        $this->assertEquals(1, $crawler->filter('ul li:contains("wallpaper.in.ua")')->count());
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
        // delete project
        $crawler = $client->request('GET', $this->getUrl('portfolioProjectDelete', array('slug' => 'preorder-it')));

        // check redirect to list of projects
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('portfolioProjectIndex', array())));
 
        // check notice
        $this->assertTrue($client->getRequest()->getSession()->hasFlash('notice'));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());
        
        // check don't display deleting category
        $this->assertEquals(0, $crawler->filter('ul li:contains("preorder.it")')->count());
    }

    public function testDeleteNotExistProject()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioProjectDelete', array('slug' => 'wallpaper-in-ua')));

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
        $this->assertEquals(1, $crawler->filter('#sidebar a[href="' . $epriceUrl . '"]')->count());
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
        $this->assertEquals(1, $crawler->filter('html #sidebar dl>dt:contains("арт-директор и дизайнер")')->count());

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
        $this->assertEquals(0, $crawler->filter('html #sidebar dl>dt:contains("арт-директор и дизайнер")')->count());
     }

}