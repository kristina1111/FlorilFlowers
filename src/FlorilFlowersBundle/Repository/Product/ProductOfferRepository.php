<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17.4.2017 г.
 * Time: 11:34 ч.
 */

namespace FlorilFlowersBundle\Repository\Product;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\Product\Product;
use FlorilFlowersBundle\Entity\Product\ProductOffer;
use FlorilFlowersBundle\Entity\User\User;

class ProductOfferRepository extends EntityRepository
{
    public function getBestSellingProductsWithQuantities()
    {
        $query = $this->_em->createQuery("SELECT cp , SUM(cp.quantity) AS quantitySold FROM FlorilFlowersBundle\Entity\Cart\CartProduct cp
JOIN FlorilFlowersBundle\Entity\Cart\Cart AS c
WITH c.id = cp.cart
JOIN FlorilFlowersBundle\Entity\Cart\Order as o
WITH o.id = c.order
WHERE o.completedOn IS NOT NULL
group by cp.offer
ORDER BY quantitySold DESC")->setMaxResults(10);
        return $query->getResult();
    }

    public function findIfUserBoughtProduct(User $user, ProductOffer $productOffer)
    {
        $query = $this->_em->createQuery("SELECT cp, SUM(cp.quantity) AS quantityBought FROM FlorilFlowersBundle\Entity\Cart\CartProduct cp 
JOIN FlorilFlowersBundle\Entity\Cart\Cart AS c
WITH c.id = cp.cart
JOIN FlorilFlowersBundle\Entity\Cart\Order as o
WITH o.id = c.order
WHERE o.user = :userId
AND o.completedOn IS NOT NULL
AND cp.offer = :productOfferId
group by cp.offer")->setParameters([
            'userId' => $user->getId(),
            'productOfferId' => $productOffer->getId()
    ]);
        return $query->getResult();
    }

    // find the sold quantity for every productOffer of the user
    public function findUserSoldProduct(ProductOffer $productOffer)
    {
        $query = $this->_em->createQuery("SELECT cp, SUM(cp.quantity) AS quantitySold FROM FlorilFlowersBundle\Entity\Cart\CartProduct cp 
JOIN FlorilFlowersBundle\Entity\Cart\Cart AS c
WITH c.id = cp.cart
JOIN FlorilFlowersBundle\Entity\Cart\Order as o
WITH o.id = c.order
AND o.completedOn IS NOT NULL
AND cp.offer = :productOfferId
group by cp.offer")->setParameters([
            'productOfferId' => $productOffer->getId()
        ]);
        return $query->getResult();
    }

    public function getProductOfferByCreatorAndProduct(User $user, Product $product)
    {
        return $this->createQueryBuilder('cp')
            ->where('cp.user = :user')
            ->andWhere('cp.product = :product')
            ->setParameters([
                'user' => $user,
                'product' => $product
            ])
            ->getQuery()
            ->execute();
    }

    public function getOfferQsInNotFinalisedCarts(ProductOffer $productOffer)
    {
//        products in not finalised carts
        $query1 = $this->_em->createQuery("
        SELECT cp, sum(cp.quantity) as quantity FROM FlorilFlowersBundle\Entity\Cart\CartProduct cp
JOIN FlorilFlowersBundle\Entity\Cart\Cart AS c
WITH c.id = cp.cart
AND c.order IS NULL
WHERE cp.offer = :productOfferId
group by cp.offer")->setParameters([
            'productOfferId' => $productOffer->getId()
        ]);

        return $query1->getResult();

    }

    public function getOfferQsInFinalisedCarts(ProductOffer $productOffer)
    {
//        products in finalised carts

        $query2 = $this->_em->createQuery("SELECT cp, sum(cp.quantity) AS quantity FROM FlorilFlowersBundle\Entity\Cart\CartProduct cp
JOIN FlorilFlowersBundle\Entity\Cart\Cart AS c
WITH c.id = cp.cart
JOIN FlorilFlowersBundle\Entity\Cart\Order as o
WITH o.id = c.order
AND c.order IS NOT NULL
AND o.confirmedOn IS NULL
WHERE cp.offer = :productOfferId
group by cp.offer")->setParameters([
            'productOfferId' => $productOffer->getId()
        ]);
        return $query2->getResult();

    }

    public function getAllOrderByRetailPrice(string $descOrAsc)
    {
        $results = $this->createQueryBuilder('po')
            ->orderBy('po.retailPrice', $descOrAsc)
            ->getQuery()
            ->execute();
        $productOffers = [];
        for ($i =0; $i<count($results); $i++){
            $productOffers[$i]['offer'] = $results[$i];
            $productOffers[$i]['retailPrice'] = $results[$i]->getRetailPrice();
        }

        return $productOffers;
    }

    public function getAllOrderByQuantityForSale(string $descOrAsc)
    {
        return $this->createQueryBuilder('po')
            ->orderBy('po.quantityForSale', $descOrAsc)
            ->getQuery()
            ->execute();
    }

    public function getAllOrderByCategory(string $descOrAsc)
    {
        return $this->createQueryBuilder('po')
            ->select('po')
            ->join('po.product', 'p')
            ->join('p.category', 'c')
            ->orderBy('c.name', $descOrAsc)
            ->getQuery()
            ->execute();
    }

    public function getAllOrderByUser(string $descOrAsc)
    {
        return $this->createQueryBuilder('po')
            ->select('po')
            ->join('po.user', 'u')
            ->join('u.role', 'r')
            ->orderBy('r.type', 'ASC') //so that always products form ADMIN and EDITOR (FlorilFlowers products) come first
            ->addOrderBy('u.nickname', $descOrAsc)
            ->getQuery()
            ->execute();
    }
}