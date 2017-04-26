<?php
///**
// * Created by PhpStorm.
// * User: user
// * Date: 10.4.2017 г.
// * Time: 19:32 ч.
// */
//
//namespace FlorilFlowersBundle\Entity\Product;
//
//
//use FlorilFlowersBundle\Entity\User\User;
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\ORM\Mapping as ORM;
//
///**
// * @ORM\Entity
// * @ORM\Table(name="tags")
// */
//class Tag
//{
//    /**
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="AUTO")
//     * @ORM\Column(type="integer")
//     */
//    private $id;
//
//    /**
//     * @ORM\Column(type="string")
//     */
//    private $name;
//
//    /**
//     * @ORM\ManyToMany(targetEntity="FlorilFlowersBundle\Entity\User\User", mappedBy="tags")
//     * @var User[]|ArrayCollection
//     */
//    private $users;
//
//    public function __construct()
//    {
//        $this->users = new ArrayCollection();
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getName()
//    {
//        return $this->name;
//    }
//
//    /**
//     * @param mixed $name
//     */
//    public function setName($name)
//    {
//        $this->name = $name;
//    }
//
//
//}