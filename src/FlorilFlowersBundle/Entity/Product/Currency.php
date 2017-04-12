<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.4.2017 г.
 * Time: 14:24 ч.
 */

namespace FlorilFlowersBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="currencies")
 */
class Currency
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
    private $type;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=5)
     */
    private $exchangeRate;
}