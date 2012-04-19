<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test cases for CategoryController
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class CategoryControllerTest extends WebTestCase
{

    public function testEmptyCategoriesList()
    {
        $this->loadFixtures(array());
        $crawler = $this->fetchCrawler($this->getUrl('portfolioCategoryIndex', array()), 'GET', true, true);

        // check display notice
        $this->assertEquals(1, $crawler->filter('html:contains("List of categories is empty")')->count());
        // check don't display categories
        $this->assertEquals(0, $crawler->filter('ul li:contains("Web Development")')->count());
    }

    public function testCategoriesList()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $crawler = $this->fetchCrawler($this->getUrl('portfolioCategoryIndex', array()), 'GET', true, true);

        // check display categories list
        $this->assertEquals(1, $crawler->filter('ul li:contains("Web Development")')->count());
    }

    public function testCreateValidCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryCreate', array()));

        $form = $crawler->selectButton('Send')->form();

        $form['category[name]'] = 'Web Design';
        $form['category[slug]'] = 'web-design';
        $form['category[description]'] = 'Short text about web design servise.';
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('portfolioCategoryIndex', array())));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        // check display new category in list
        $this->assertEquals(1, $crawler->filter('ul li:contains("Web Design")')->count());
    }

    public function testCreateInvalidCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryCreate', array()));

        $form = $crawler->selectButton('Send')->form();

        $form['category[name]'] = ''; // should not be blank
        $form['category[slug]'] = ''; // should not be blank
        $form['category[description]'] = ''; // should not be blank
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertFalse($client->getResponse()->isRedirect());
    }

    public function testEditCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $client = $this->makeClient(true);

        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryEdit', array('slug' => 'web-development')));

        $form = $crawler->selectButton('Save')->form();

        $form['category[name]'] = 'Web Design';
        $form['category[slug]'] = 'web-design';
        $form['category[description]'] = 'Short text about web design servise.';
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('portfolioCategoryIndex', array())));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        $this->assertEquals(1, $crawler->filter('ul li:contains("Web Design")')->count());
    }

    public function testViewCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolioCategoryView', array('slug' => 'web-development')), 'GET', true, true
        );

        $this->assertEquals(1, $crawler->filter('html:contains("Web Development")')->count());
        $description = "In work we use Symfony2.";
        $this->assertEquals(1, $crawler->filter('html:contains("' . $description . '")')->count());
    }

    public function testViewNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryView', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testEditInvalidCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $client = $this->makeClient(true);

        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryEdit', array('slug' => 'web-development')));

        $form = $crawler->selectButton('Save')->form();

        $form['category[name]'] = 'Web Design';
        $form['category[slug]'] = 'web-design';
        $form['category[description]'] = '';
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertFalse($client->getResponse()->isRedirect());
    }

    public function testEditNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);

        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryEdit', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));

        $client = $this->makeClient(true);
        // delete project
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryDelete', array('slug' => 'web-development')));

        // check redirect to list of categories
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('portfolioCategoryIndex', array())));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        // check notice
//        $this->assertTrue($client->getRequest()->getSession()->hasFlash('notice'));

        // check don't display deleting category
        $this->assertEquals(0, $crawler->filter('ul li:contains("Web Development")')->count());
    }

    public function testDeleteNotExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryDelete', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

     public function testListProjectsByCategory()
     {
        $this->loadFixtures(array(
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData'
        ));
        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolioProjectsByCategory', array('slug' => 'web-development')), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('h4:contains("Projects in category: Web Development")')->count());

        $this->assertEquals(8, $crawler->filter('#listProjects li')->count());
        $this->assertEquals(1, $crawler->filter('#listProjects li:contains("preorder.it")')->count());
        $this->assertEquals(7, $crawler->filter('#listProjects li:contains("eprice.kz")')->count());
     }

     public function testPortfolioPagination()
     {
        $this->loadFixtures(array(
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData'
        ));
        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolioCategoryView', array('slug' => 'web-development')), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("1")')->count());
        $this->assertEquals(6, $crawler->filter('img.project-thumb')->count());

        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolioCategoryView', array('slug' => 'web-development', 'page'=> 2)), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("2")')->count());
        $this->assertEquals(2, $crawler->filter('img.project-thumb')->count());
     }

     public function testProjectOrdering()
     {
        $crawler = $this->fetchCrawler($this->getUrl('portfolioProjectIndex'), 'GET', true, true);

        $lis = $crawler->filter('#sortable li');
        $lis->first();

        $this->assertContains('preorder.it', $lis->current()->textContent);

        $client = $this->makeClient(true);
        $crawler = $client->request('POST', $this->getUrl('portfolioProjectsApplyOrder'), array(
            'projects' => array(array('id' => $lis->current()->getAttribute('data-id'), 'index' => 200))
        ));
        $this->assertEquals('good', $client->getResponse()->getContent());

        $crawler = $this->fetchCrawler($this->getUrl('portfolioProjectIndex'), 'GET', true, true);
        $lis = $crawler->filter('#sortable li');
        $lis->last();
        $this->assertContains('preorder.it', $lis->current()->textContent);
     }
}
