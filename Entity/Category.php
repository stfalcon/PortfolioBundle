<?php

namespace Stfalcon\Bundle\PortfolioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category entity. It groups projects in portfolio
 *
 * @ORM\MappedSuperclass
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(3)
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name = '';

    /**
     * @var string $slug
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(3)
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     */
    protected $slug;

    /**
     * @var string $description
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(10)
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @var ArrayCollection
     */
    protected $projects;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category name
     *
     * @param string $name Text for category name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get category name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set category slug
     *
     * @param string $slug Unique text identifier
     *
     * @return void
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get category slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set category description
     *
     * @param string $description Text for category description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get category description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get category projects
     *
     * @return ArrayCollection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set projects
     *
     * @param ArrayCollection $projects Array collection of projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    /**
     * This method allows a class to decide how it will react when it is treated like a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}