<?php

namespace Stfalcon\Bundle\PortfolioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Stfalcon\Bundle\PortfolioBundle\Repository\CategoryRepository")
 * @ORM\Table(name="portfolio_categories_base")
 */
class Category extends BaseCategory
{
    /**
     * @var ArrayCollection $projects
     *
     * @ORM\ManyToMany(
     *      targetEntity="Stfalcon\Bundle\PortfolioBundle\Entity\Project",
     *      mappedBy="categories", fetch="EXTRA_LAZY"
     * )
     * @ORM\OrderBy({"ordernum" = "ASC", "date" = "DESC"})
     */
    protected $projects;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="ordernum", type="integer")
     */
    protected $ordernum = 0;

    /**
     * Initialization properties for new category entity
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    /**
     * Add project to category
     *
     * @param Project $project Project object
     */
    public function addProject($project)
    {
        $this->projects->add($project);
    }



    /**
     * Get order num
     *
     * @return integer
     */
    public function getOrdernum()
    {
        return $this->ordernum;
    }

    /**
     * Set order num
     *
     * @param integer $ordernum
     */
    public function setOrdernum($ordernum)
    {
        $this->ordernum = $ordernum;
    }
}
