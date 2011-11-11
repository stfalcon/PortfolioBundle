<?php

namespace Stfalcon\Bundle\PortfolioBundle\Tests\Entity;

use Stfalcon\Bundle\PortfolioBundle\Entity\Project;
use Stfalcon\Bundle\PortfolioBundle\Entity\Category;

class ProjectEntityTest extends \PHPUnit_Framework_TestCase
{

    private function _getTestImagePath()
    {
        return \realpath(__DIR__ . '/Resources/files/projects/preorder-it/data/index.png');
    }

    public function testEmptyProjectIdisNull()
    {
        $project = new Project();
        $this->assertNull($project->getId());
    }

    public function testSetAndGetProjectName()
    {
        $name = "preorder.it";
        
        $project = new Project();
        $project->setName($name);

        $this->assertEquals($project->getName(), $name);
    }

    public function testSetAndGetProjectDescription()
    {
        $description = "Press-releases and reviews of the latest electronic novelties: mobile phones, cellphones, smartphones, laptops, tablets, netbooks, gadgets, e-books, photo and video cameras. The possibility to leave a pre-order.";

        $project = new Project();
        $project->setDescription($description);

        $this->assertEquals($project->getDescription(), $description);
    }

    public function testSetAndGetProjectDate()
    {
        $project = new Project();

        $date = new \DateTime('now');
        $project->setDate($date);

        $this->assertEquals($project->getDate(), $date);
    }

    public function testPathToUploadsIsDirAndIsWritable()
    {
        $project = new Project();
        $project->setPathToUploads(realpath(__DIR__ . '/../uploads'));

        $this->assertTrue(\is_dir($project->getPathToUploads()));
        $this->assertTrue(\is_writable($project->getPathToUploads()));
    }

    public function testSetAndGetProjectImage()
    {
        $project = new Project();
        $project->setPathToUploads(realpath(__DIR__ . '/../uploads'));
        
        $this->assertTrue(\file_exists($this->_getTestImagePath()));

        $project->setImage($this->_getTestImagePath());
        $this->assertTrue(\file_exists($project->getImage()));

        // remove test image file
        $project->removeImage();
    }

    public function testRemoveImageMethod()
    {
        $project = new Project();
        $project->setPathToUploads(realpath(__DIR__ . '/../uploads'));

        // try remove not exist image
        $this->assertFalse($project->removeImage());

        $project->setImage($this->_getTestImagePath());

        $imagePath = $project->getImage();

        // try remove exist image
        $this->assertTrue($project->removeImage());
        // check or image does not exist
        $this->assertFalse(\file_exists($imagePath));
    }

    public function testRemoveOldImageWhenUpdating()
    {
        $project = new Project();
        $project->setPathToUploads(realpath(__DIR__ . '/../uploads'));

        $this->assertTrue(\file_exists($this->_getTestImagePath()));

        // upload first image
        $project->setImage($this->_getTestImagePath());
        $firstImagePath = $project->getImage();
        $this->assertTrue(\file_exists($firstImagePath), 'Image file is not exist');

        // upload second image
        $project->setImage($this->_getTestImagePath());
        $secondImagePath = $project->getImage();
        // check or old file does not exist
        $this->assertFalse(\file_exists($firstImagePath), 'Old image file is exist');
        // check or new file is exist
        $this->assertTrue(\file_exists($secondImagePath), 'New image file is not exist');

        // remove test image file
        $project->removeImage();
    }

    public function testGetProjectCreated()
    {
        $project = new Project();

        $this->assertEquals($project->getCreated(), null);
    }

    public function testGetProjectUpdated()
    {
        $project = new Project();

        $this->assertEquals($project->getUpdated(), null);
    }

    public function testSetAndGetProjectSlug()
    {
        $project = new Project();

        $slug = 'preorder-it';
        $project->setSlug($slug);

        $this->assertEquals($project->getSlug(), $slug);
    }

    public function testSetAndGetProjectUrl()
    {
        $project = new Project();

        $url = 'http://preorder.it';
        $project->setUrl($url);

        $this->assertEquals($project->getUrl(), $url);
    }

    public function testSetAndGetAndAddProjectCategories()
    {
        $project = new Project();

        $category = new Category();

        $project->addCategory($category);
        $categories = $project->getCategories();
        
        $this->assertEquals($categories->count(), 1);
        $this->assertTrue(\is_a($categories, 'Doctrine\Common\Collections\ArrayCollection'), 2);

        $project->setCategories($categories);
        
        $this->assertEquals($project->getCategories(), $categories);
    }

    public function testSetAndGetProjectUsers()
    {
        $users = '<ul class="comandList"><li><h5>арт-директор и дизайнер<span>Олег Пащенко</span></h5></li></ul>';

        $project = new Project();
        $project->setUsers($users);

        $this->assertEquals($project->getUsers(), $users);
    }
}