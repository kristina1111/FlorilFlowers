<?php
//
//namespace FlorilFlowersBundle\Entity\Product;
//
//use FlorilFlowersBundle\Entity\User\User;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Product\ProductReviewRepository")
// * @ORM\Table(name="product_reviews")
// */
//class ProductReview
//{
//    /**
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="AUTO")
//     * @ORM\Column(type="integer")
//     */
//    private $id;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="productReviews")
//     * @ORM\JoinColumn(nullable=false)
//     * @var User
//     */
//    private $user;
//
//    /**
//     * @ORM\Column(type="text")
//     */
//    private $review;
//
//    /**
//     * @ORM\Column(type="datetime")
//     */
//    private $createdAt;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", inversedBy="reviews")
//     * @ORM\JoinColumn(nullable=false)
//     * @var ProductOffer
//     */
//    private $productOffer;
//
//    public function __construct()
//    {
//        $this->createdAt = new \DateTime();
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
//
//    /**
//     * @return User
//     */
//    public function getUser()
//    {
//        return $this->user;
//    }
//
//    /**
//     * @var User
//     * @param $user
//     */
//    public function setUser($user)
//    {
//        $this->user = $user;
//    }
//
//    /**
//     * @return string
//     */
//    public function getReview()
//    {
//        return $this->review;
//    }
//
//
//    public function setReview($review)
//    {
//        $this->review = $review;
//    }
//
//    public function getCreatedAt()
//    {
//        return $this->createdAt;
//    }
//
//    /**
//     * @param mixed $createdAt
//     */
//    public function setCreatedAt($createdAt)
//    {
//        $this->createdAt = $createdAt;
//    }
//
//
//    /**
//     * @return ProductOffer
//     */
//    public function getProductOffer()
//    {
//        return $this->productOffer;
//    }
//
//    /**
//     * @param ProductOffer $productOffer
//     */
//    public function setProductOffer(ProductOffer $productOffer)
//    {
//        $this->productOffer = $productOffer;
//    }
//
//
//}