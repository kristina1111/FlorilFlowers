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
use FlorilFlowersBundle\Entity\User\User;

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

    // finds all the products that the user ever bought with their quantities
    //returns array with arrays - innerArray[0] is the object, innerArray["quantityBought"] is the quantity
    public function selectAllBoughtProductsWithQuantity(User $user)
    {
        $query = $this->_em->createQuery("SELECT cp , SUM(cp.quantity) AS quantityBought FROM FlorilFlowersBundle\Entity\Cart\CartProduct cp
JOIN FlorilFlowersBundle\Entity\Cart\Cart AS c
WITH c.id = cp.cart
JOIN FlorilFlowersBundle\Entity\Cart\Order as o
WITH o.id = c.order
WHERE o.user = :userId
AND o.completedOn IS NOT NULL
group by cp.offer
ORDER BY quantityBought DESC
")->setParameter('userId', $user->getId());
        return $query->getResult();
    }


}