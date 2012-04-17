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

    /**
     * test Empty Categories List
     */
    public function testEmptyCategoriesList()
    {
        $this->loadFixtures(array());
        $crawler = $this->fetchCrawler($this->getUrl('portfolioCategoryIndex', array()), 'GET', true, true);

        // check display notice
        $this->assertEquals(1, $crawler->filter('html:contains("List of categories is empty")')->count());
        // check don't display categories
        $this->assertEquals(0, $crawler->filter('ul li:contains("Web Development")')->count());
    }

    /**
     *  test Categories List
     */
    public function testCategoriesList()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $crawler = $this->fetchCrawler($this->getUrl('portfolioCategoryIndex', array()), 'GET', true, true);

        // check display categories list
        $this->assertEquals(1, $crawler->filter('ul li:contains("Web Development")')->count());
    }

    /**
     *  Create Valid Category
     */
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

    /**
     *  Create Invalid Category
     */
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

    /**
     * Edit category
     */
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

    /**
     * view category
     */
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

    /**
     *  Viewing Non-Existing Category
     */
    public function testViewNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryView', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     *  Edit Invalid Category
     */
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

    /**
     *  Edit Non-Exist Category
     */
    public function testEditNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);

        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryEdit', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * delete category
     */
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

    /**
     *  Delete Not-Exist Category
     */
    public function testDeleteNotExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolioCategoryDelete', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

}
