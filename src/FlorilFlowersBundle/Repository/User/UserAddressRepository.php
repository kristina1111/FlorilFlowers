<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.4.2017 г.
 * Time: 12:43 ч.
 */

namespace FlorilFlowersBundle\Repository\User;


use Doctrine\ORM\EntityRepository;
use FlorilFlowersBundle\Entity\User\User;

class UserAddressRepository extends EntityRepository
{
    public function getUserAddresses(User $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user);
    }
}