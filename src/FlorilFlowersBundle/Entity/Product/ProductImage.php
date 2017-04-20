<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.4.2017 г.
 * Time: 15:32 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Please, upload the product image as jpeg/jpg/png file.")
     * @Assert\File(
     *     maxSize = "5M" ,
     *     mimeTypes={ "image/png", "image/jpeg", "image/jpg"} ,
     *     mimeTypesMessage = "Please upload a valid image (png, jpeg/jpg)!"
     *     )
     * @var UploadedFile
     * This property is not persisted in the db. It only contains the file during the handling of the form.
     */
    private $file;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadedOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", inversedBy="productImages")
     * @ORM\JoinColumn(nullable=false)
     * @var ProductOffer
     */
    private $productOffer;

    /**
     * @var bool
     * Necessary to check if the image that is being uploaded is defined by the user as front image of the product offer
     * This will not be persisted in the db.
     */
    private $isFrontImage;

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


    public function getPath()
    {
        return $this->path;
    }


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
     * @return bool
     */
    public function getIsFrontImage()
    {
        return $this->isFrontImage;
    }

    /**
     * @param bool $isFrontImage
     */
    public function setIsFrontImage($isFrontImage)
    {
        $this->isFrontImage = $isFrontImage;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    public function isExistingInDb(ArrayCollection $originalPrices){

    }


}