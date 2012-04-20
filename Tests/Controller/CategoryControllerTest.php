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

     public function _testProjectOrdering()
     {
        $crawler = $this->fetchCrawler($this->getUrl('portfolioProjectIndex'), 'GET', true, true);

        $lis = $crawler->filter('#projects-sortable li');
        $lis->first();

        $this->assertContains('preorder.it', $lis->current()->textContent);

        $client = $this->makeClient(true);
        $crawler = $client->request('POST', $this->getUrl('portfolioProjectsApplyOrder'), array(
            'projects' => array(array('id' => $lis->current()->getAttribute('data-id'), 'index' => 200))
        ));
        $this->assertEquals('good', $client->getResponse()->getContent());

        $crawler = $this->fetchCrawler($this->getUrl('portfolioProjectIndex'), 'GET', true, true);
        $lis = $crawler->filter('#projects-sortable li');
        $lis->last();
        $this->assertContains('preorder.it', $lis->current()->textContent);
     }
}
