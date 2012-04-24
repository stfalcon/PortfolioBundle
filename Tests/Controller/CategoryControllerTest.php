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
        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_category_list', array()), 'GET', true, true);

        // check display notice
        $this->assertEquals(0, $crawler->filter('table tbody tr')->count());
    }

    public function testCategoriesList()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_category_list', array()), 'GET', true, true);

        // check display categories list
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("web-development")')->count());
    }

    public function testCreateValidCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_category_create', array()));

        $inputs = $crawler->filter('form input');
        $inputs->first();
        $formId = str_replace("_slug", "", $inputs->current()->getAttribute('id'));

        $form = $crawler->selectButton('Создать и редактировать')->form();

        $form[$formId . '[name]'] = 'Web Design';
        $form[$formId . '[slug]'] = 'web-design';
        $form[$formId . '[description]'] = 'Short text about web design servise.';
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('admin_bundle_portfolio_category_edit', array('id' => 1) )));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_category_list', array()), 'GET', true, true);

        // check display new category in list
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("web-design")')->count());
    }

    public function testCreateInvalidCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_category_create', array()));

        $inputs = $crawler->filter('form input');
        $inputs->first();
        $formId = str_replace("_slug", "", $inputs->current()->getAttribute('id'));

        $form = $crawler->selectButton('Создать и редактировать')->form();

        $form[$formId . '[name]'] = ''; // should not be blank
        $form[$formId . '[slug]'] = ''; // should not be blank
        $form[$formId . '[description]'] = ''; // should not be blank
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertFalse($client->getResponse()->isRedirect());
    }

    public function testEditCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $client = $this->makeClient(true);

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $category = $em->getRepository("StfalconPortfolioBundle:Category")->findOneBy(array('slug' => 'web-development'));
        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_category_edit', array('id' => $category->getId())));

        $inputs = $crawler->filter('form input');
        $inputs->first();
        $formId = str_replace("_slug", "", $inputs->current()->getAttribute('id'));

        $form = $crawler->selectButton('Сохранить')->form();

        $form[$formId . '[name]'] = 'Web Design';
        $form[$formId . '[slug]'] = 'web-design';
        $form[$formId . '[description]'] = 'Short text about web design servise.';
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertTrue($client->getResponse()->isRedirect($this->getUrl('admin_bundle_portfolio_category_edit', array('id' => $category->getId()) )));

        $crawler = $client->followRedirect();

        // check responce
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse($client->getResponse()->isRedirect());

        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_category_list', array()), 'GET', true, true);
        $this->assertEquals(1, $crawler->filter('table tbody tr td:contains("web-design")')->count());
    }

    public function testViewCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development')), 'GET', true, true
        );

        $this->assertEquals(1, $crawler->filter('html:contains("Web Development")')->count());
        $description = "In work we use Symfony2.";
        $this->assertEquals(1, $crawler->filter('html:contains("' . $description . '")')->count());
    }

    public function testViewNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolio_category_view', array('slug' => 'web-design')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testEditInvalidCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $client = $this->makeClient(true);

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $category = $em->getRepository("StfalconPortfolioBundle:Category")->findOneBy(array('slug' => 'web-development'));
        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_category_edit', array('id' => $category->getId())));

        $inputs = $crawler->filter('form input');
        $inputs->first();
        $formId = str_replace("_slug", "", $inputs->current()->getAttribute('id'));

        $form = $crawler->selectButton('Сохранить')->form();

        $form[$formId . '[name]'] = 'Web Design';
        $form[$formId . '[slug]'] = 'web-design';
        $form[$formId . '[description]'] = '';
        $crawler = $client->submit($form);

        // check redirect to list of categories
        $this->assertFalse($client->getResponse()->isRedirect());
    }

    public function testEditNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);

        $crawler = $client->request('GET', $this->getUrl('admin_bundle_portfolio_category_edit', array('id' => 0)));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDeleteCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));

        $client = $this->makeClient(true);
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $category = $em->getRepository("StfalconPortfolioBundle:Category")->findOneBy(array('slug' => 'web-development'));

        // delete category
        $crawler = $client->request('POST', $this->getUrl('admin_bundle_portfolio_category_delete', array('id' => $category->getId())), array('_method' => 'DELETE'));

        // check if category was removed from DB
        $em->detach($category);
        $categoryRemoved = $em->getRepository("StfalconPortfolioBundle:Category")->findOneBy(array('slug' => 'web-development'));
        $this->assertNull($categoryRemoved);
    }

    public function testDeleteNotExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('POST', $this->getUrl('admin_bundle_portfolio_category_delete', array('id' => 0)));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

     public function _testListProjectsByCategory()
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
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development')), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("1")')->count());
        $this->assertEquals(6, $crawler->filter('img.project-thumb')->count());

        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development', 'page'=> 2)), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("2")')->count());
        $this->assertEquals(2, $crawler->filter('img.project-thumb')->count());
     }

     public function testProjectOrdering()
     {
        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_project_list'), 'GET', true, true);

        $tds = $crawler->filter('table tbody tr');
        $tds->first();
        $this->assertContains('preorder.it', $tds->current()->textContent);

        $client = $this->makeClient(true);
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $project = $em->getRepository("StfalconPortfolioBundle:Project")->findOneBy(array('name' => 'preorder.it'));
        $crawler = $client->request('POST', $this->getUrl('portfolioProjectsApplyOrder'), array(
            'projects' => array(array('id' => $project->getId(), 'index' => 200))
        ));
        $this->assertEquals('good', $client->getResponse()->getContent());

        $crawler = $this->fetchCrawler($this->getUrl('admin_bundle_portfolio_project_list'), 'GET', true, true);
        $tds = $crawler->filter('table tbody tr');
        $tds->last();
        $this->assertContains('preorder.it', $tds->current()->textContent);
     }
}
