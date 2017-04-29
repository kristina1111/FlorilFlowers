<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.4.2017 г.
 * Time: 12:28 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FlorilFlowersBundle\Repository\Product\ProductOfferOrderRepository")
 * @ORM\Table(name="product_offer_view_orders")
 */
class ProductOfferOrder
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
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $activatedOn;


    /**
     * @ORM\Column(type="boolean")
     * @ORM\JoinColumn(nullable=true)
     */
    private $descOrAsc;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getActivatedOn()
    {
        return $this->activatedOn;
    }

    /**
     * @param mixed $activatedOn
     */
    public function setActivatedOn($activatedOn)
    {
        $this->activatedOn = $activatedOn;
    }

    /**
     * @return mixed
     */
    public function getDescOrAsc()
    {
        return $this->descOrAsc;
    }

    /**
     * @param mixed $descOrAsc
     */
    public function setDescOrAsc($descOrAsc)
    {
        $this->descOrAsc = $descOrAsc;
    }

    function __toString()
    {
        return $this->name;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


}