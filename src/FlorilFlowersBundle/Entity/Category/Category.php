<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5.4.2017 г.
 * Time: 14:20 ч.
 */

namespace FlorilFlowersBundle\Entity\Category;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Product\Product;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Category\CategoryRepository")
 * @ORM\Table(name="categories")
 */
class Category
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
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\Product", mappedBy="category")
     * @var Product[]|ArrayCollection
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Category\Subcategory", mappedBy="category")
     * @var Subcategory[]|ArrayCollection
     */
    private $subcategories;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->subcategories = new ArrayCollection();
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
     * @return Product[] | ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return ArrayCollection|Subcategory[]
     */
    public function getSubcategories()
    {
        return $this->subcategories;
    }

    /**
     * @param ArrayCollection|Subcategory[] $subcategories
     */
    public function setSubcategories($subcategories)
    {
        $this->subcategories = $subcategories;
    }



    public function __toString()
    {
        return $this->getName();
    }

}