<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.4.2017 г.
 * Time: 21:28 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use FlorilFlowersBundle\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Product\ProductOfferRepository")
 * @ORM\Table(name="product_offers")
 */
class ProductOffer
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
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantityForSale;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="productOffers")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\Product", inversedBy="productOffers")
     * @ORM\JoinColumn(nullable=false)
     * @var Product
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductPrice", mappedBy="productOffer")
     * @var ProductPrice[]|ArrayCollection
     */
    private $productPrices;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOfferReview", mappedBy="productOffer")
     * @var ProductOfferReview[]|ArrayCollection
     */
    private $productOfferReviews;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Product\ProductImage", mappedBy="productOffer")
     * @var ProductImage[]|ArrayCollection
     */
    private $productImages;

    public function __construct()
    {
        $this->createdOn = new \DateTime();
        $this->productOfferReviews = new ArrayCollection();
        $this->productPrices = new ArrayCollection();
        $this->productImages = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return mixed
     */
    public function getQuantityForSale()
    {
        return $this->quantityForSale;
    }

    /**
     * @param mixed $quantityForSale
     */
    public function setQuantityForSale($quantityForSale)
    {
        $this->quantityForSale = $quantityForSale;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return ProductPrice[]|ArrayCollection
     */
    public function getProductPrices()
    {
        return $this->productPrices;
    }

    /**
     * @param ProductPrice[]|ArrayCollection $productPrices
     */
    public function setProductPrices($productPrices)
    {
        $this->productPrices = $productPrices;
    }

    /**
     * @return ProductOfferReview[]|ArrayCollection
     */
    public function getProductOfferReviews()
    {
        return $this->productOfferReviews;
    }

    /**
     * @param ProductOfferReview[]|ArrayCollection $productOfferReviews
     */
    public function setProductOfferReviews($productOfferReviews)
    {
        $this->productOfferReviews = $productOfferReviews;
    }

    /**
     * @return ProductImage[]|ArrayCollection
     */
    public function getProductImages()
    {
        return $this->productImages;
    }

    /**
     * @param ProductImage[]|ArrayCollection $productImages
     */
    public function setProductImages($productImages)
    {
        $this->productImages = $productImages;
    }


}