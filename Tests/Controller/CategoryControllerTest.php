<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Controller;

//use Stfalcon\Bundle\BlogBundle\Tests\Controller\AbstractTestCase;
//use Liip\FunctionalTestBundle\Test\WebTestCase;
use Application\Bundle\DefaultBundle\Tests\Controller\AbstractTestCase;

/**
 * Test cases for CategoryController
 */
class CategoryControllerTest extends AbstractTestCase
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

        $this->paginationCheck('portfolio_category_view', 'slug', 'web-development', 'project-thumb', 6);

        /*$crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development')), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("1")')->count());
        $this->assertEquals(6, $crawler->filter('img.project-thumb')->count());

        $crawler = $this->fetchCrawler(
            $this->getUrl('portfolio_category_view', array('slug' => 'web-development', 'page'=> 2)), 'GET', true, true
        );
        $this->assertEquals(1, $crawler->filter('.pagination .current:contains("2")')->count());
        $this->assertEquals(2, $crawler->filter('img.project-thumb')->count());*/
    }

}