<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 25.4.2017 г.
 * Time: 22:57 ч.
 */

namespace FlorilFlowersBundle\Repository\Cart;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\User\User;

class OrderRepository extends EntityRepository
{
//    public function findByUserAndNotCompleted(User $user)
//    {
//        return $this->createQueryBuilder('o')
//            ->where('o.user = :user')
//            ->setParameter('user', $user)
//            ->andWhere('o.completedOn is NULL')
//            ->getQuery()
//            ->execute();
//    }
}