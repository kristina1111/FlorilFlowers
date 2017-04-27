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
    public function findByUserAndNotConfirmed(User $user)
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.confirmedOn is NULL')
            ->orderBy('o.createdOn', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findByUserAndConfirmedNotCompleted(User $user)
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.confirmedOn is not NULL')
            ->andWhere('o.completedOn is NULL')
            ->orderBy('o.confirmedOn', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findByUserAndCompleted(User $user)
    {
        return $this->createQueryBuilder('o')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.completedOn is not NULL')
            ->orderBy('o.completedOn', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findAllNotConfirmed()
    {
        return $this->createQueryBuilder('o')
            ->where('o.completedOn is NULL')
            ->orderBy('o.confirmedOn', 'DESC')
            ->getQuery()
            ->execute();
    }


}