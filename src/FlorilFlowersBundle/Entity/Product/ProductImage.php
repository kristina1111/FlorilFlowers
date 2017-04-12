<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.4.2017 г.
 * Time: 15:32 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_images")
 */
class ProductImage
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
    private $path;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadedOn;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deletedOn;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", inversedBy="productImages")
     * @ORM\JoinColumn(nullable=false)
     * @var ProductOffer
     */
    private $productOffer;

    public function __construct()
    {
        $this->uploadedOn = new \DateTime();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getUploadedOn()
    {
        return $this->uploadedOn;
    }


    public function setUploadedOn($uploadedOn)
    {
        $this->uploadedOn = $uploadedOn;
    }


    public function getDeletedOn()
    {
        return $this->deletedOn;
    }

    public function setDeletedOn($deletedOn)
    {
        $this->deletedOn = $deletedOn;
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