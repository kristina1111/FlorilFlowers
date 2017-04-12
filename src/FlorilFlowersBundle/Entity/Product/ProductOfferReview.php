<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.4.2017 г.
 * Time: 11:49 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use FlorilFlowersBundle\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_offer_reviews")
 */
class ProductOfferReview
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $reviewText;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="productOfferReviews")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", inversedBy="productOfferReviews")
     * @ORM\JoinColumn(nullable=false)
     * @var ProductOffer
     */
    private $productOffer;

    public function __construct()
    {
        $this->createdOn = new \DateTime();
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
    public function getReviewText()
    {
        return $this->reviewText;
    }

    /**
     * @param mixed $reviewText
     */
    public function setReviewText($reviewText)
    {
        $this->reviewText = $reviewText;
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
     * @return ProductOffer
     */
    public function getProductOffer()
    {
        return $this->productOffer;
    }

    /**
     * @param ProductOffer $productOffer
     */
    public function setProductOffer($productOffer)
    {
        $this->productOffer = $productOffer;
    }



}