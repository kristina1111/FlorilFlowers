<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2017 г.
 * Time: 14:16 ч.
 */

namespace FlorilFlowersBundle\Entity\Cart;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\User\User;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Cart\CartRepository")
 * @ORM\Table(name="carts")
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="carts")
     * @var User
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="FlorilFlowersBundle\Entity\Cart\Order", inversedBy="cart")
     * @var Order
     */
    private $order;

    /**
     * @ORM\OneToMany(targetEntity="FlorilFlowersBundle\Entity\Cart\CartProduct", mappedBy="cart")
     * @var CartProduct[]|ArrayCollection
     */
    private $cartProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return CartProduct[]|ArrayCollection
     */
    public function getCartProducts()
    {
        return $this->cartProducts;
    }

    /**
     * @param ArrayCollection|CartProduct[] $cartProducts
     */
    public function setCartProducts($cartProducts)
    {
        $this->cartProducts = $cartProducts;
    }


}