<?php

namespace FlorilFlowersBundle\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Category\Category;
//use FlorilFlowersBundle\Entity\Category\Subcategory;


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
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Category\Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

//    /**
//     * Many products has one subcategory
//     * @var Subcategory
//     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Category\Subcategory", inversedBy="products")
//     * @ORM\JoinColumn(nullable=false)
//     */
//    private $subcategory;

//    /**
//     * @ORM\Column(type="string")
//     */
//    private $unitMeasure;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;


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
//    public function getUnitMeasure()
//    {
//        return $this->unitMeasure;
//    }
//
//    /**
//     * @param mixed $unitMeasure
//     */
//    public function setUnitMeasure($unitMeasure)
//    {
//        $this->unitMeasure = $unitMeasure;
//    }

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

//    /**
//     * @return Subcategory
//     */
//    public function getSubcategory()
//    {
//        return $this->subcategory;
//    }
//
//    /**
//     * @param Subcategory $subcategory
//     */
//    public function setSubcategory($subcategory)
//    {
//        $this->subcategory = $subcategory;
//    }

    /**
     * @return ArrayCollection|ProductOffer[]
     */
    public function getProductOffers()
    {
        return $this->productOffers;
    }

    /**
     * @param ArrayCollection|ProductOffer[] $productOffers
     */
    public function setProductOffers($productOffers)
    {
        $this->productOffers = $productOffers;
    }




}