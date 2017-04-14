<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.4.2017 г.
 * Time: 12:03 ч.
 */

namespace FlorilFlowersBundle\Entity\Category;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Product\Product;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Category\Subcategory")
 * @ORM\Table(name="subcategories")
 */
class Subcategory
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
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Category\Category", inversedBy="subcategories")
     * @var Category
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\Product", mappedBy="subcategory")
     * @var Product[]|ArrayCollection
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    /**
     * @return ArrayCollection|Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection|Product[] $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function __toString()
    {
        return $this->getName();
    }
}