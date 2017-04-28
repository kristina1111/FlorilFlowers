<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.4.2017 г.
 * Time: 12:35 ч.
 */

namespace FlorilFlowersBundle\Entity\Promotion;


use Doctrine\ORM\Mapping as ORM;
use FlorilFlowersBundle\Entity\Category\Category;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\Role;
use FlorilFlowersBundle\Entity\User\User;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Promotion\PromotionRepository")
 * @ORM\Table(name="promotions")
 */
class Promotion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(type="string", nullable= false)
     */
    private $name;

    /**
     * @var string $description
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;


    /**
     * @var integer $percent
     * @ORM\Column(type="integer")
     */
    private $percent;

    /**
     * @var \DateTime $startDate
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime $endDate
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Category\Category")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Category $category
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\Product\ProductOffer")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var ProductOffer $productOffer
     */
    private $productOffer;

    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\Role")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @var Role $role
     */
    private $role;


    /**
     * @ORM\ManyToOne(targetEntity="FlorilFlowersBundle\Entity\User\User")
     * @ORM\JoinColumn(nullable=true)
     * @var User $user
     */
    private $user;

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

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @param int $percent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;
    }


    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }


    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getProductOffer()
    {
        return $this->productOffer;
    }

    /**
     * @param ProductOffer $productOffer
     */
    public function setProductOffer(ProductOffer $productOffer)
    {
        $this->productOffer = $productOffer;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
    }


}