<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11.4.2017 г.
 * Time: 18:36 ч.
 */

namespace FlorilFlowersBundle\Entity\User;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_phones")
 */
class UserPhone
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
    private $phoneNumber;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User", inversedBy="phones")
     * @ORM\JoinColumn(nullable=false)
     * @var User
     */
    private $user;
}