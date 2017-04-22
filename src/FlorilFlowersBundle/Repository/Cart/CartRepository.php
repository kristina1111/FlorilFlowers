<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.4.2017 г.
 * Time: 21:52 ч.
 */

namespace FlorilFlowersBundle\Repository\Cart;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\User\User;

class CartRepository extends EntityRepository
{
    public function findCartByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->andWhere('c.order is NULL')
            ->getQuery()
            ->execute();
    }
}