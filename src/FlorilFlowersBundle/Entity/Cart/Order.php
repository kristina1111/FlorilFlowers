<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2017 г.
 * Time: 14:22 ч.
 */

namespace FlorilFlowersBundle\Entity\Cart;


use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\User\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="orders")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $completedOn;

    /**
     * @ORM\OneToOne(targetEntity="FlorilFlowersBundle\Entity\Cart\Order", mappedBy="cart")
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
    public function getCompletedOn()
    {
        return $this->completedOn;
    }

    /**
     * @param mixed $completedOn
     */
    public function setCompletedOn($completedOn)
    {
        $this->completedOn = $completedOn;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Cart $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }


}