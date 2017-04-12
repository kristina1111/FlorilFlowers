<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.4.2017 г.
 * Time: 14:20 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product_prices")
 */
class ProductPrice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=5)
     */
    private $retailPrice;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\Currency")
     * @var Currency
     */
    private $currency;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer", inversedBy="productPrices")
     * @var ProductOffer
     */
    private $productOffer;

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
    public function getRetailPrice()
    {
        return $this->retailPrice;
    }

    /**
     * @param mixed $retailPrice
     */
    public function setRetailPrice($retailPrice)
    {
        $this->retailPrice = $retailPrice;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return ProductOffer
     */
    public function getProductOffer()
    {
        return $this->productOffer;
    }

    /**
     * @param ProductOffer $ProductOffer
     */
    public function setProductOffer($ProductOffer)
    {
        $this->productOffer = $ProductOffer;
    }


}