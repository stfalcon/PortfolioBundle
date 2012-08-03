<?php

namespace Stfalcon\Bundle\PortfolioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo,
    Gedmo\Translatable\Translatable;

/**
 * Category entity. It groups projects in portfolio
 *
 * @ORM\Table(name="portfolio_categories")
 * @ORM\Entity(repositoryClass="Stfalcon\Bundle\PortfolioBundle\Repository\CategoryRepository")
 * @Gedmo\TranslationEntity(class="Stfalcon\Bundle\PortfolioBundle\Entity\CategoryTranslation")
 */
class Category implements Translatable
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * //@Assert\NotBlank()
     * //@Assert\MinLength(3)
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string $slug
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(3)
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     */
    private $slug;

    /**
     * @var string $description
     *
     * //@Assert\NotBlank()
     * //@Assert\MinLength(10)
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(
     *      targetEntity="Stfalcon\Bundle\PortfolioBundle\Entity\Project",
     *      mappedBy="categories", fetch="EXTRA_LAZY"
     * )
     * @ORM\OrderBy({"ordernum" = "ASC", "date" = "DESC"})
     */
    private $projects;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordernum", type="integer")
     */
    private $ordernum = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *   targetEntity="CategoryTranslation",
     *   mappedBy="object",
     *   cascade={"persist", "remove"}
     * )
     */
    private $translations;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;

    /**
     * Initialization properties for new category entity
     *
     * @return Category
     */
    public function __construct()
    {
        $this->projects     = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * Get category id
     *
     * @return integer
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
     * Add project to category
     *
     * @param Project $project Project object
     *
     * @return void
     */
    public function addProject(Project $project)
    {
        $this->projects[] = $project;
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

    /**
     * Gets translation
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param CategoryTranslation $translation
     */
    public function addTranslation(CategoryTranslation $translation)
    {
        $this->translations[] = $translation;
        $translation->setObject($this);
    }

    /**
     * @param $translations
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    /**
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}