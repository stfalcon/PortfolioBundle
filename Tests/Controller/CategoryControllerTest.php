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

    public function testPortfolioPagination()
    {
        $this->loadFixtures(array(
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadCategoryData',
            'Stfalcon\Bundle\PortfolioBundle\DataFixtures\ORM\LoadProjectData'
        ));

        // check elements on second page
        $crawlerFirstPage = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development'))
        );
        $this->assertCount(1, $crawlerFirstPage->filter('.pagination .current:contains("1")'));
        $this->assertCount(6, $crawlerFirstPage->filter('img.project-thumb'));

        // check elements on second page
        $crawlerSecondPage = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development', 'page'=> 2))
        );
        $this->assertCount(1, $crawlerSecondPage->filter('.pagination .current:contains("2")'));
        $this->assertCount(2, $crawlerSecondPage->filter('img.project-thumb'));
     }

}