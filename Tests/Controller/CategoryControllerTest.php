<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test cases for CategoryController
 */
class CategoryControllerTest extends WebTestCase
{

    public function testViewCategory()
    {
        $this->loadFixtures(array('Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData'));
        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development', '_locale' => 'ru')), 'GET', true, true
        );

        $this->assertEquals(1, $crawler->filter('html:contains("Web Development")')->count());
        $description = "In work we use Symfony2.";
        $this->assertEquals(1, $crawler->filter('html:contains("' . $description . '")')->count());
    }

    public function testViewNonExistCategory()
    {
        $this->loadFixtures(array());
        $client = $this->makeClient(true);
        $crawler = $client->request('GET', $this->getUrl('portfolio_category_view', array('slug' => 'web-design', '_locale' => 'ru')));

        // check 404
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testPortfolioPagination()
    {
        $this->loadFixtures(array(
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData'
        ));
        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development', '_locale' => 'ru')), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("1")')->count());
        $this->assertEquals(6, $crawler->filter('img.project-thumb')->count());

        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development','_locale' => 'ru', 'page'=> 2)), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("2")')->count());
        $this->assertEquals(2, $crawler->filter('img.project-thumb')->count());
     }

}