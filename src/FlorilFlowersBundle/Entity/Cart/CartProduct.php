<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2017 г.
 * Time: 15:17 ч.
 */

namespace FlorilFlowersBundle\Entity\Cart;


use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Product\ProductOffer;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Cart\CartProductRepository")
 * @ORM\Table(name="cart_products")
 */
class CartProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer")
     * @var ProductOffer
     */
    private $offer;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Cart\Cart", inversedBy="cartProducts")
     * @var Cart
     */
    private $cart;

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
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param mixed $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param mixed $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }


}