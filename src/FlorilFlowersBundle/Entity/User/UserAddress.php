<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.4.2017 г.
 * Time: 18:08 ч.
 */

namespace FlorilFlowersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\User\UserAddressRepository")
 * @ORM\Table(name="user_addresses")
 */
class UserAddress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $address;

//    /**
//     * @ORM\Column(type="boolean")
//     */
//    private $isShipmentAddress;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="addresses")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private $user;

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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

//    /**
//     * @return mixed
//     */
//    public function getIsShipmentAddress()
//    {
//        return $this->isShipmentAddress;
//    }
//
//    /**
//     * @param mixed $isShipmentAddress
//     */
//    public function setIsShipmentAddress($isShipmentAddress)
//    {
//        $this->isShipmentAddress = $isShipmentAddress;
//    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


}