<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 22.4.2017 г.
 * Time: 10:49 ч.
 */

namespace FlorilFlowersBundle\Repository\Cart;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\Cart\Cart;
use FlorilFlowersBundle\Entity\Product\ProductOffer;

class CartProductRepository extends EntityRepository
{
    public function findByCartAndProductOffer(Cart $cart, ProductOffer $productOffer)
    {
        return $this->createQueryBuilder('cp')
            ->where('cp.cart = :cart')
            ->setParameter('cart', $cart)
            ->andWhere('cp.offer = :productOffer')
            ->setParameter('productOffer', $productOffer)
            ->getQuery()
            ->execute();
    }
}