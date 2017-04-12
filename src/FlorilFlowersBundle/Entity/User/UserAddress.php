<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.4.2017 г.
 * Time: 18:08 ч.
 */

namespace FlorilFlowersBundle\Entity\User;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     */
    private $address;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isShipmentAddress;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="addresses")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private $user;
}