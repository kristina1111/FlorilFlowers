<?php

namespace FlorilFlowersBundle\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Product\ProductRepository")
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * Many products has one category
     * @var Category
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string")
     */
    private $unitMeasure;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductReview", mappedBy="product")
     * it's still only one relation in the DB but this allows another way to access the data on it
     *
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", mappedBy="product")
     * @var ProductOffer[]|ArrayCollection
     */
    private $productOffers;

    /**
     * ProductReview constructor.
     */
    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->productOffers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUnitMeasure()
    {
        return $this->unitMeasure;
    }

    /**
     * @param mixed $unitMeasure
     */
    public function setUnitMeasure($unitMeasure)
    {
        $this->unitMeasure = $unitMeasure;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param mixed $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return ArrayCollection|ProductReview[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


}