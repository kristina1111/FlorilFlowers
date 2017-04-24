<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3.4.2017 г.
 * Time: 17:40 ч.
 */

namespace FlorilFlowersBundle\Repository\Product;

use FlorilFlowersBundle\Entity\Product\Product;
use Doctrine\ORM\EntityRepository;
use Faker\Provider\cs_CZ\DateTime;

class ProductOfferReviewRepository extends EntityRepository
{
    public function findAllRecentNotesForProduct(Product $product)
    {
        return $this->createQueryBuilder('product_review')
            ->andWhere('product_review.product = :product')
            ->setParameter('product', $product)
            ->andWhere('product_review.createdAt > :date')
            ->setParameter('date', new \DateTime('-3 months'))
            ->getQuery()
            ->execute();
    }
}