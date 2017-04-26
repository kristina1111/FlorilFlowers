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
use FlorilFlowersBundle\Entity\User\UserAddress;
use FlorilFlowersBundle\Entity\User\UserPhone;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Cart\OrderRepository")
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmedOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    private $completedOn;

    /**
     * @ORM\OneToOne(targetEntity="FlorilFlowersBundle\Entity\Cart\Cart", mappedBy="order")
     * @var Cart
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\UserAddress")
     * @Assert\NotBlank()
     * @var UserAddress
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\UserPhone")
     * @Assert\NotBlank()
     * @var UserPhone
     */
    private $phone;

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

    /**
     * @return UserAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param UserAddress $address
     */
    public function setAddress(UserAddress $address)
    {
        $this->address = $address;
    }

    /**
     * @return UserPhone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param UserPhone $phone
     */
    public function setPhone(UserPhone $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return \DateTime
     */
    public function getConfirmedOn()
    {
        return $this->confirmedOn;
    }

    /**
     * @param \DateTime $confirmedOn
     */
    public function setConfirmedOn($confirmedOn)
    {
        $this->confirmedOn = $confirmedOn;
    }


}