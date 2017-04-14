<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3.4.2017 г.
 * Time: 15:36 ч.
 */

namespace FlorilFlowersBundle\Repository\Product;


use FlorilFlowersBundle\Entity\Product\Product;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    /**
     * @return Product[]
     */
    public function findAllPublishedOrderedByQuantity()
    {
        return $this->createQueryBuilder('product')
            ->getQuery()
            ->execute();
    }
}